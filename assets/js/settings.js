jQuery(document).ready(function($) {
  /**
   * Global variables.
   */
  var woosupercharge_cd_available = [];
  var woosupercharge_cd_selected = [];
  var woosupercharge_cd_total = 0;

  // Populate woosupercharge_cd_available array
  for (let key in availableConditions) {
      if (availableConditions.hasOwnProperty(key)) {
          woosupercharge_cd_total++;
          let element = availableConditions[key];
          woosupercharge_cd_available[key] = element;
      }
  }

  // Initialize selected conditions
  woosupercharge_cd_repopulate_selected();

  // Show/hide options based on selected conditions
  woosupercharge_cd_hideshow_selected();

  // Check if all conditions are used and hide the add button accordingly
  woosupercharge_cd_check_add_button();

  // Repopulate selected conditions array
  function woosupercharge_cd_repopulate_selected() {
      woosupercharge_cd_selected = [];
      $('table#woosupercharge-conditions-table > tbody > tr.single_condition').each(function() {
          let currentValue = $(this).find('select.select_condition option:selected').val();
          woosupercharge_cd_selected.push(currentValue);
      });
  }

  // Show/hide options based on woosupercharge_cd_selected
  function woosupercharge_cd_hideshow_selected() {
      $('table#woosupercharge-conditions-table > tbody > tr.single_condition select.select_condition option:not(:selected)').each(function() {
          if (woosupercharge_cd_selected.includes($(this).val())) {
              $(this).hide();
          } else {
              $(this).show();
          }
      });
  }

  // Check if all conditions are used and show/hide the add button accordingly
  function woosupercharge_cd_check_add_button() {
      if (woosupercharge_cd_selected.length === woosupercharge_cd_total) {
          $('.woosupercharge-add-condition').hide();
      } else {
          $('.woosupercharge-add-condition').show();
      }
  }

  // Add a new condition
  $(document).on('click', '.woosupercharge-add-condition', function(e) {
      e.preventDefault();
      e.stopPropagation();

      woosupercharge_cd_repopulate_selected();

      let count = Math.random().toString(36).slice(2, 11);

      let html = '';
      html += '<tr class="single_condition">';
      html += '<td>';
      html += '<select class="select_condition" name="woosupercharge-display-conditions-settings[display_conditions][' + count + ']">';

      let hasSelected = true;
      for (let key in woosupercharge_cd_available) {
          html += '<option value="' + key + '" ';
          if (woosupercharge_cd_selected.includes(key)) {
              html += 'style="display:none;"';
          } else if (hasSelected) {
              html += 'selected';
              hasSelected = false;
          }
          html += '> ' + woosupercharge_cd_available[key] + ' </option>';
      }

      html += '</select>';
      html += '</td>';
      html += '<td><span class="woosupercharge-rmv-condition">&#10060</span></td>';
      html += '</tr>';

      $('#woosupercharge-conditions-table tbody').append(html);

      woosupercharge_cd_repopulate_selected();
      woosupercharge_cd_hideshow_selected();
      woosupercharge_cd_check_add_button();
  });

  // Remove the added condition
  $(document).on('click', '.woosupercharge-rmv-condition', function(e) {
      $(this).closest('tr').remove();
      woosupercharge_cd_repopulate_selected();
      woosupercharge_cd_hideshow_selected();
      woosupercharge_cd_check_add_button();
  });

  // Select option changed
  $(document).on('change', 'table#woosupercharge-conditions-table > tbody > tr.single_condition select.select_condition', function(e) {
      woosupercharge_cd_repopulate_selected();
      woosupercharge_cd_hideshow_selected();
  });

  // Slider functionality
  const $slider = $('#slider');
  const $sliderValueInput = $('#sliderValue');

  $slider.on('input', updateSliderValue);
  $sliderValueInput.on('input', updateSliderFromInput);

  function updateSliderValue() {
      const sliderValue = $slider.val();
      $sliderValueInput.val(sliderValue);
  }

  function updateSliderFromInput() {
      const inputValue = $sliderValueInput.val();
      $slider.val(inputValue);
  }

  // Initial slider update
  updateSliderValue();

  // Position handling
  $(".position-select-container label").on("click", function(e) {
      $(".position-select-container label").removeClass("active");
      $(this).addClass("active");
  });
});
