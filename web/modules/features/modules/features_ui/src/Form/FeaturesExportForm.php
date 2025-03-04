<?php

namespace Drupal\features_ui\Form;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;
use Drupal\features\FeaturesAssignerInterface;
use Drupal\features\FeaturesGeneratorInterface;
use Drupal\features\FeaturesManagerInterface;
use Drupal\features\FeaturesBundleInterface;
use Drupal\features\Package;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the configuration export form.
 */
class FeaturesExportForm extends FormBase implements TrustedCallbackInterface {

  /**
   * The features manager.
   *
   * @var array
   */
  protected $featuresManager;

  /**
   * The package assigner.
   *
   * @var array
   */
  protected $assigner;

  /**
   * The package generator.
   *
   * @var array
   */
  protected $generator;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a FeaturesExportForm object.
   *
   * @param \Drupal\features\FeaturesManagerInterface $features_manager
   *   The features manager.
   * @param \Drupal\features\FeaturesAssignerInterface $assigner
   *   The features assigner.
   * @param \Drupal\features\FeaturesGeneratorInterface $generator
   *   The features generator.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The features generator.
   */
  public function __construct(FeaturesManagerInterface $features_manager, FeaturesAssignerInterface $assigner, FeaturesGeneratorInterface $generator, ModuleHandlerInterface $module_handler) {
    $this->featuresManager = $features_manager;
    $this->assigner = $assigner;
    $this->generator = $generator;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('features.manager'),
      $container->get('features_assigner'),
      $container->get('features_generator'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return [
      'preRenderRemoveInvalidCheckboxes',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'features_export_form';
  }

  /**
   * Detects if an element triggered the form submission via Ajax.
   * TODO: SHOULDN'T NEED THIS!  BUT DRUPAL IS CALLING buildForm AFTER THE
   * BUNDLE AJAX IS SELECTED AND DOESN'T HAVE getTriggeringElement() SET YET.
   */
  protected function elementTriggeredScriptedSubmission(FormStateInterface &$form_state) {
    $input = $form_state->getUserInput();
    if (!empty($input['_triggering_element_name'])) {
      return $input['_triggering_element_name'];
    }
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $trigger = $form_state->getTriggeringElement();
    // TODO: See if there is a Drupal Core issue for this.
    // Sometimes the first ajax call on the page causes buildForm to be called
    // twice!  First time form_state->getTriggeringElement is NOT SET, but
    // the form_state['input'] shows the _triggering_element_name.  Then the
    // SECOND time it is called the getTriggeringElement is fine.
    $real_trigger = $this->elementTriggeredScriptedSubmission($form_state);
    if (!isset($trigger) && ($real_trigger == 'bundle')) {
      $input = $form_state->getUserInput();
      $bundle_name = $input['bundle'];
      $this->assigner->setCurrent($this->assigner->getBundle($bundle_name));
    }
    elseif (isset($trigger['#name']) && $trigger['#name'] == 'bundle') {
      $bundle_name = $form_state->getValue('bundle', '');
      $this->assigner->setCurrent($this->assigner->getBundle($bundle_name));
    }
    else {
      $this->assigner->loadBundle();
    }
    $current_bundle = $this->assigner->getBundle();
    $this->assigner->assignConfigPackages();

    $packages = $this->featuresManager->getPackages();
    $config_collection = $this->featuresManager->getConfigCollection();

    // Add in un-packaged configuration items.
    $this->addUnpackaged($packages, $config_collection);

    $packages = $this->featuresManager->filterPackages($packages, $current_bundle->getMachineName());

    // Pass the packages and bundle data for use in the form pre_render
    // callback.
    $form['#packages'] = $packages;
    $form['#profile_package'] = $current_bundle->getProfileName();
    $form['header'] = [
      '#type' => 'container',
      '#attributes' => ['class' => 'features-header'],
    ];

    $bundle_options = $this->assigner->getBundleOptions();

    // If there are no custom bundles, provide message.
    if (count($bundle_options) < 2) {
      $this->messenger()->addStatus($this->t('You have not yet created any bundles. Before generating features, you may wish to <a href=":create">create a bundle</a> to group your features within.', [':create' => Url::fromRoute('features.assignment')->toString()]));
    }

    $form['#prefix'] = '<div id="edit-features-wrapper">';
    $form['#suffix'] = '</div>';
    $form['header']['bundle'] = [
      '#title' => $this->t('Bundle'),
      '#type' => 'select',
      '#options' => $bundle_options,
      '#default_value' => $current_bundle->getMachineName(),
      '#prefix' => '<div id="edit-package-set-wrapper">',
      '#suffix' => '</div>',
      '#ajax' => [
        'callback' => '::updatePreview',
        'wrapper' => 'edit-features-preview-wrapper',
      ],
      '#attributes' => [
        'data-new-package-set' => 'status',
      ],
    ];

    $form['preview'] = $this->buildListing($packages, $current_bundle);

    $form['#attached'] = [
      'library' => [
        'features_ui/drupal.features_ui.admin',
      ],
    ];

    if (\Drupal::currentUser()->hasPermission('export configuration')) {
      // Offer available generation methods.
      $generation_info = $this->generator->getGenerationMethods();
      // Sort generation methods by weight.
      uasort($generation_info, '\Drupal\Component\Utility\SortArray::sortByWeightElement');

      $form['description'] = [
        '#markup' => '<p>' . $this->t('Use an export method button below to generate the selected features.') . '</p>',
      ];

      $form['actions'] = ['#type' => 'actions', '#tree' => TRUE];
      foreach ($generation_info as $method_id => $method) {
        $form['actions'][$method_id] = [
          '#type' => 'submit',
          '#name' => $method_id,
          '#value' => $this->t('@name', ['@name' => $method['name']]),
          '#attributes' => [
            'title' => Html::escape($method['description']),
          ],
        ];
      }
    }

    $form['#pre_render'][] = [get_class($this), 'preRenderRemoveInvalidCheckboxes'];

    return $form;
  }

  /**
   * Handles switching the configuration type selector.
   */
  public function updatePreview($form, FormStateInterface $form_state) {
    // We should really be able to add this pre_render callback to the
    // 'preview' element. However, since doing so leads to an error (no rows
    // are displayed), we need to instead explicitly invoke it here for the
    // processing to apply to the Ajax-rendered form element.
    $form = $this->preRenderRemoveInvalidCheckboxes($form);
    return $form['preview'];
  }

  /**
   * Builds the portion of the form showing a listing of features.
   *
   * @param \Drupal\features\Package[] $packages
   *   The packages.
   * @param \Drupal\features\FeaturesBundleInterface $bundle
   *   The current bundle.
   *
   * @return array
   *   A render array of a form element.
   */
  protected function buildListing(array $packages, FeaturesBundleInterface $bundle) {

    $header = [
      'name' => ['data' => $this->t('Feature')],
      'machine_name' => ['data' => $this->t('')],
      'details' => ['data' => $this->t('Description'), 'class' => [RESPONSIVE_PRIORITY_LOW]],
      'version' => ['data' => $this->t('Version'), 'class' => [RESPONSIVE_PRIORITY_LOW]],
      'status' => ['data' => $this->t('Status'), 'class' => [RESPONSIVE_PRIORITY_LOW]],
      'state' => ['data' => $this->t('State'), 'class' => [RESPONSIVE_PRIORITY_LOW]],
    ];

    $options = [];
    $first = TRUE;
    foreach ($packages as $package) {
      if ($first && $package->getStatus() == FeaturesManagerInterface::STATUS_NO_EXPORT) {
        // Don't offer new non-profile packages that are empty.
        if ($package->getStatus() === FeaturesManagerInterface::STATUS_NO_EXPORT &&
          !$bundle->isProfilePackage($package->getMachineName()) &&
          empty($package->getConfig())) {
          continue;
        }
        $first = FALSE;
        $options[] = [
          'name' => [
            'data' => $this->t('The following packages are not exported.'),
            'class' => 'features-export-header-row',
            'colspan' => 6,
          ],
        ];
      }
      $options[$package->getMachineName()] = $this->buildPackageDetail($package, $bundle);
    }

    $element = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $options,
      '#attributes' => ['class' => ['features-listing']],
      '#prefix' => '<div id="edit-features-preview-wrapper">',
      '#suffix' => '</div>',
    ];

    return $element;
  }

  /**
   * Builds the details of a package.
   *
   * @param \Drupal\features\Package $package
   *   The package.
   * @param \Drupal\features\FeaturesBundleInterface $bundle
   *   The current bundle.
   *
   * @return array
   *   A render array of a form element.
   */
  protected function buildPackageDetail(Package $package, FeaturesBundleInterface $bundle) {
    $config_collection = $this->featuresManager->getConfigCollection();

    $url = Url::fromRoute('features.edit', ['featurename' => $package->getMachineName()]);

    $element['name'] = [
      'data' => \Drupal::service('link_generator')->generate($package->getName(), $url),
      'class' => ['feature-name'],
    ];
    $machine_name = $package->getMachineName();
    // Except for the 'unpackaged' pseudo-package, display the full name, since
    // that's what will be generated.
    if ($machine_name !== 'unpackaged') {
      $machine_name = $bundle->getFullName($machine_name);
    }
    $element['machine_name'] = $machine_name;
    $element['status'] = [
      'data' => $this->featuresManager->statusLabel($package->getStatus()),
      'class' => ['column-nowrap'],
    ];
    // Use 'data' instead of plain string value so a blank version doesn't
    // remove column from table.
    $element['version'] = [
      'data' => Html::escape($package->getVersion()),
      'class' => ['column-nowrap'],
    ];
    $overrides = $this->featuresManager->detectOverrides($package);
    $new_config = $this->featuresManager->detectNew($package);
    $conflicts = [];
    $missing = [];
    $moved = [];

    if ($package->getStatus() == FeaturesManagerInterface::STATUS_NO_EXPORT) {
      $overrides = [];
      $new_config = [];
    }
    // Bundle package configuration by type.
    $package_config = [];
    foreach ($package->getConfig() as $item_name) {
      if (isset($config_collection[$item_name])) {
        $item = $config_collection[$item_name];
        $package_config[$item->getType()][] = [
          'name' => Html::escape($item_name),
          'label' => Html::escape($item->getLabel()),
          'class' => in_array($item_name, $overrides) ? 'features-override' : (in_array($item_name, $new_config) ? 'features-detected' : ''),
        ];
      }
    }
    // Conflict config from other modules.
    foreach ($package->getConfigOrig() as $item_name) {
      if (!isset($config_collection[$item_name])) {
        $missing[] = $item_name;
        $package_config['missing'][] = [
          'name' => Html::escape($item_name),
          'label' => Html::escape($item_name),
          'class' => 'features-missing',
        ];
      }
      elseif (!in_array($item_name, $package->getConfig())) {
        $item = $config_collection[$item_name];
        if (empty($item->getProvider())) {
          $conflicts[] = $item_name;
          $package_name = !empty($item->getPackage()) ? $item->getPackage() : $this->t('PACKAGE NOT ASSIGNED');
          $package_config[$item->getType()][] = [
            'name' => Html::escape($package_name),
            'label' => Html::escape($item->getLabel()),
            'class' => 'features-conflict',
          ];
        }
        else {
          $moved[] = $item_name;
          $package_name = !empty($item->getPackage()) ? $item->getPackage() : $this->t('PACKAGE NOT ASSIGNED');
          $package_config[$item->getType()][] = [
            'name' => $this->t('Moved to @package', ['@package' => $package_name]),
            'label' => Html::escape($item->getLabel()),
            'class' => 'features-moved',
          ];
        }
      }
    }
    // Add dependencies.
    $package_config['dependencies'] = [];
    foreach ($package->getDependencies() as $dependency) {
      $package_config['dependencies'][] = [
        'name' => $dependency,
        'label' => $this->moduleHandler->moduleExists($dependency) ? \Drupal::service('extension.list.module')->getName($dependency) : $dependency,
        'class' => '',
      ];
    }

    $class = '';
    $state_links = [];
    if (!empty($conflicts)) {
      $state_links[] = [
        '#type' => 'link',
        '#title' => $this->t('Conflicts'),
        '#url' => Url::fromRoute('features.edit', ['featurename' => $package->getMachineName()]),
        '#attributes' => ['class' => ['features-conflict']],
      ];
    }
    if (!empty($overrides)) {
      $state_links[] = [
        '#type' => 'link',
        '#title' => $this->featuresManager->stateLabel(FeaturesManagerInterface::STATE_OVERRIDDEN),
        '#url' => Url::fromRoute('features.diff', ['featurename' => $package->getMachineName()]),
        '#attributes' => ['class' => ['features-override']],
      ];
    }
    if (!empty($new_config)) {
      $state_links[] = [
        '#type' => 'link',
        '#title' => $this->t('New detected'),
        '#url' => Url::fromRoute('features.diff', ['featurename' => $package->getMachineName()]),
        '#attributes' => ['class' => ['features-detected']],
      ];
    }
    if (!empty($missing) && ($package->getStatus() == FeaturesManagerInterface::STATUS_INSTALLED)) {
      $state_links[] = [
        '#type' => 'link',
        '#title' => $this->t('Missing'),
        '#url' => Url::fromRoute('features.edit', ['featurename' => $package->getMachineName()]),
        '#attributes' => ['class' => ['features-missing']],
      ];
    }
    if (!empty($moved)) {
      $state_links[] = [
        '#type' => 'link',
        '#title' => $this->t('Moved'),
        '#url' => Url::fromRoute('features.edit', ['featurename' => $package->getMachineName()]),
        '#attributes' => ['class' => ['features-moved']],
      ];
    }
    if (!empty($state_links)) {
      $element['state'] = [
        'data' => $state_links,
        'class' => ['column-nowrap'],
      ];
    }
    else {
      $element['state'] = '';
    }

    $config_types = $this->featuresManager->listConfigTypes();
    // Add dependencies.
    $config_types['dependencies'] = $this->t('Dependencies');
    $config_types['missing'] = $this->t('Missing');
    uasort($config_types, 'strnatcasecmp');

    $rows = [];
    // Use sorted array for order.
    foreach ($config_types as $type => $label) {
      // For each component type, offer alternating rows.
      $row = [];
      if (isset($package_config[$type])) {
        $row[] = [
          'data' => [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => Html::escape($label),
            '#attributes' => [
              'title' => Html::escape($type),
              'class' => 'features-item-label',
            ],
          ],
        ];
        $row[] = [
          'data' => [
            '#theme' => 'features_items',
            '#items' => $package_config[$type],
            '#value' => Html::escape($label),
            '#title' => Html::escape($type),
          ],
          'class' => 'item',
        ];
        $rows[] = $row;
      }
    }
    $element['table'] = [
      '#type' => 'table',
      '#rows' => $rows,
    ];

    $details = [];
    $details['description'] = [
      '#markup' => Xss::filterAdmin($package->getDescription()),
    ];
    $details['table'] = [
      '#type' => 'details',
      '#title' => $this->t('Included configuration'),
      '#description' => ['data' => $element['table']],
    ];
    $element['details'] = [
      'class' => ['description', 'expand'],
      'data' => $details,
    ];

    return $element;
  }

  /**
   * Adds a pseudo-package to display unpackaged configuration.
   *
   * @param array $packages
   *   An array of package names.
   * @param \Drupal\features\ConfigurationItem[] $config_collection
   *   A collection of configuration.
   */
  protected function addUnpackaged(array &$packages, array $config_collection) {
    $packages['unpackaged'] = new Package('unpackaged', [
      'name' => $this->t('Unpackaged'),
      'description' => $this->t('Configuration that has not been added to any package.'),
      'config' => [],
      'status' => FeaturesManagerInterface::STATUS_NO_EXPORT,
      'version' => '',
    ]);
    foreach ($config_collection as $item_name => $item) {
      if (!$item->getPackage() && !$item->isExcluded() && !$item->isProviderExcluded()) {
        $packages['unpackaged']->appendConfig($item_name);
      }
    }
  }

  /**
   * Denies access to the checkboxes for uninstalled or empty packages and the
   * "unpackaged" pseudo-package.
   *
   * @param array $form
   *   The form build array to alter.
   *
   * @return array
   *   The form build array.
   */
  public static function preRenderRemoveInvalidCheckboxes(array $form) {
    /** @var \Drupal\features\Package $package */
    foreach ($form['#packages'] as $package) {
      // Remove checkboxes for packages that:
      // - have no configuration assigned and are not the profile, or
      // - are the "unpackaged" pseudo-package.
      if ((empty($package->getConfig()) && !($package->getMachineName() == $form['#profile_package'])) ||
        $package->getMachineName() == 'unpackaged') {
        $form['preview'][$package->getMachineName()]['#access'] = FALSE;
      }
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $current_bundle = $this->assigner->loadBundle();
    $this->assigner->assignConfigPackages();

    $package_names = array_filter($form_state->getValue('preview'));

    if (empty($package_names)) {
      $this->messenger()->addWarning($this->t('Please select one or more packages to export.'));
      return;
    }

    $method_id = NULL;
    $trigger = $form_state->getTriggeringElement();
    $op = $form_state->getValue('op');
    if (!empty($trigger) && empty($op)) {
      $method_id = $trigger['#name'];
    }

    if (!empty($method_id)) {
      $this->generator->generatePackages($method_id, $current_bundle, $package_names);
      $this->generator->applyExportFormSubmit($method_id, $form, $form_state);
    }
  }

}
