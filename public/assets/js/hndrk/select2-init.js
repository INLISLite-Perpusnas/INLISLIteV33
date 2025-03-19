$(document).ready(function () {
  $("#opt_material_type").select2({
    placeholder: "Pilih Jenis Bahan",
    theme: "bootstrap",
  });
  $("#opt_rda").select2({
    placeholder: "RDA/AACR",
    theme: "bootstrap",
  });
  $(".opt_main_creator").select2({
    placeholder: "kreator utama",
    theme: "bootstrap",
  });
  $(".opt-addition-creator").select2({
    placeholder: "kreator tambahan",
    theme: "bootstrap",
  });
  $(".dropdown-subject").select2({
    placeholder: "Nama Orang",
    theme: "bootstrap",
  });
  $(".opt").select2({
    theme: "bootstrap",
    placeholder: $(this).data("placeholder"),
  });

  $("#opt_type").select2({
    ajax: {
      dataType: "JSON",
      method: "POST",
      type: "public",
      delay: 1000,
      url: "http://localhost/dev-bmn/public/repo/getselecttype",
      data: function (param) {
        return { term: param.term };
      },
      processResults: function (data) {
        return { results: data };
      },
    },
    inputTooShort: "Please add more text",
    placeholder: "Pilih Jenis",
    minimumInputLength: 3,
    templateResult: formatRepo,
    templateSelection: formatRepoSelection,
  });
});

function formatRepo(data) {
  console.log(data);
  return data.name;
}
function formatRepoSelection(data) {
  return data.name || data.text;
}
