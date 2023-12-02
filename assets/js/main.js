(function ($) {
  $(document).ready(function () {
    $("#wpila_other_email").change(function () {
      if ($(this).prop("checked")) {
        $(".wpila-other").removeClass("disabled-div");
      } else {
        $(".wpila-other").addClass("disabled-div");
      }
    });
  });
})(jQuery);
