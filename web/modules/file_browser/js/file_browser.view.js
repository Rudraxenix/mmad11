/**
 * @file
 * file_browser.view.js
 */

(($, Drupal) => {
  /**
   * Renders the file counter based on our internally tracked count.
   */
  function renderFileCounter() {
     document
      .querySelectorAll('.file-browser-file-counter')
      .forEach((el) => el.remove());

    const counter = {};

    document
      .querySelectorAll('.entities-list [data-entity-id]')
      .forEach((element) => {
        const entityId = element.dataset.entityId;
        if (counter[entityId]) {
          counter[entityId] += 1;
        } else {
          counter[entityId] = 1;
        }
      });

    Object.keys(counter).forEach((id) => {
      const count = counter[id];
      if (count > 0) {
        const text = Drupal.formatPlural(
          count,
          'Selected one time',
          'Selected @count times',
        );
        const counterElement = document.createElement('div');
        counterElement.className = 'file-browser-file-counter';
        counterElement.textContent = text;

        const gridItemInfo = document
          .querySelector(`[name = "entity_browser_select[file:${id}]"]`)
          .closest('.grid-item')
          .querySelector('.grid-item-info');

        if (gridItemInfo) {
          gridItemInfo.insertAdjacentElement('afterbegin', counterElement);
        }
      }
    });
  }

  /**
   * Adjusts the padding on the body to account for the fixed actions bar.
   */
  function adjustBodyPadding() {
    setTimeout(() => {
      const bodyElement = document.querySelector('body');
      const actionsElement = document.querySelector('.file-browser-actions');
      const actionsHeight = actionsElement ? actionsElement.offsetHeight : 0;
      bodyElement.style.paddingBottom = `${actionsHeight}px`;
    }, 2000);
  }

  /**
   * Initializes Masonry for the view widget.
   */
  Drupal.behaviors.fileBrowserMasonry = {
    attach(context) {
      const $item = $('.grid-item', context);
      const view = once('file-browser-init', $item.parent());
      if (view.length) {
        const $view = $(view);
        $view.prepend(
          '<div class="grid-sizer"></div><div class="gutter-sizer"></div>',
        );

        // Indicate that images are loading.
        $view.append(
          '<div class="ajax-progress ajax-progress-fullscreen">&nbsp;</div>',
        );
        $view.imagesLoaded(() => {
          // Save the scroll position.
          const scroll = document.body.scrollTop;
          // Remove old Masonry object if it exists. This allows modules like
          // Views Infinite Scroll to function with File Browser.
          if ($view.data('masonry')) {
            $view.masonry('destroy');
          }
          $view.masonry({
            columnWidth: '.grid-sizer',
            gutter: '.gutter-sizer',
            itemSelector: '.grid-item',
            percentPosition: true,
            isFitWidth: true,
          });
          // Jump to the old scroll position.
          document.body.scrollTop = scroll;
          // Add a class to reveal the loaded images, which avoids FOUC.
          $item.addClass('item-style');
          $view.find('.ajax-progress').remove();
        });
      }
    },
  };

  /**
   * Checks the hidden Entity Browser checkbox when an item is clicked.
   *
   * This behavior provides backwards-compatibility for users not using
   * auto-select and multi-step.
   */
  Drupal.behaviors.fileBrowserClickProxy = {
    attach(context, settings) {
      if (!settings.entity_browser_widget.auto_select) {
        $(once('bind-click-event', '.grid-item', context)).click(() => {
          const input = $(this).find(
            '.views-field-entity-browser-select input',
          );
          input.prop('checked', !input.prop('checked'));
          if (input.prop('checked')) {
            $(this).addClass('checked');
          } else {
            $(this).removeClass('checked');
          }
        });
      }
    },
  };

  /**
   * Tracks when entities have been added or removed in the multi-step form,
   * and displays that information on each grid item.
   */
  Drupal.behaviors.fileBrowserEntityCount = {
    attach(context) {
      adjustBodyPadding();
      renderFileCounter();
      // Indicate when files have been selected.
      const entities = once(
        'file-browser-add-count',
        '.entities-list',
        context,
      );
      if (entities.length) {
        entities.forEach((entity) => {
          entity.addEventListener('add-entities', () => {
            adjustBodyPadding();
            renderFileCounter();
          });
        });
        entities.forEach((entity) => {
          entity.addEventListener('remove-entities', () => {
            adjustBodyPadding();
            renderFileCounter();
          });
        });
      }
    },
  };
})(jQuery, Drupal);
