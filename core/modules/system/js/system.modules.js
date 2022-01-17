/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function ($, Drupal, debounce) {
  Drupal.behaviors.tableFilterByText = {
    attach(context, settings) {
      const [input] = once('table-filter-text', 'input.table-filter-text');

      if (!input) {
        return;
      }

      const $table = $(input.getAttribute('data-table'));
      let $rowsAndDetails;
      let $rows;
      let $details;
      let searching = false;

      function hidePackageDetails(index, element) {
        const $packDetails = $(element);
        const $visibleRows = $packDetails.find('tbody tr:visible');
        $packDetails.toggle($visibleRows.length > 0);
      }

      function filterModuleList(e) {
        const query = e.target.value;
        const re = new RegExp(`\\b${query}`, 'i');

        function showModuleRow(index, row) {
          const $row = $(row);
          const $sources = $row.find('.table-filter-text-source, .module-name, .module-description');
          const textMatch = $sources.text().search(re) !== -1;
          $row.closest('tr').toggle(textMatch);
        }

        $rowsAndDetails.show();

        if (query.length >= 2) {
          searching = true;
          $rows.each(showModuleRow);
          $details.not('[open]').attr('data-drupal-system-state', 'forced-open');
          $details.attr('open', true).each(hidePackageDetails);
          Drupal.announce(Drupal.t('!modules modules are available in the modified list.', {
            '!modules': $rowsAndDetails.find('tbody tr:visible').length
          }));
        } else if (searching) {
          searching = false;
          $rowsAndDetails.show();
          $details.filter('[data-drupal-system-state="forced-open"]').removeAttr('data-drupal-system-state').attr('open', false);
        }
      }

      function preventEnterKey(event) {
        if (event.which === 13) {
          event.preventDefault();
          event.stopPropagation();
        }
      }

      if ($table.length) {
        $rowsAndDetails = $table.find('tr, details');
        $rows = $table.find('tbody tr');
        $details = $rowsAndDetails.filter('.package-listing');
        $(input).on({
          keyup: debounce(filterModuleList, 200),
          keydown: preventEnterKey
        });
      }
    }

  };
})(jQuery, Drupal, Drupal.debounce);