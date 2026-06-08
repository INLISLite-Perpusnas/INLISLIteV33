$(window).on("load", function (e) {
  var data = [
    {
      worksheet: "",
      previousfreq: "",
      currentfreq: "",
      title: { title: "", childtitle: "", heldby: "", previoustitle: [] },
      page: "",
      illustration: "",
      dimention: "",
      accompanyingmaterial: "",
      notes: [{ radio: "", input: "" }],
      location: [],
      lang: "###",
      paper: "0",
      target: "#",
      subject: [],
      ddc: "",
      callnumber: [],
      issn: [],
      author: { select: "1", input: "", radio: "0" },
      additionalAuthor: [{ select: "1", input: "", radio: "0" }],
      name: "",
      year: "",
      place: "",
    },
  ];
  localStorage.setItem("data", JSON.stringify(data));
  console.log(JSON.parse(localStorage.getItem("data")));
});

$(document).ready(function () {
  let formapp = $("#form-app"),
    ajax,
    dataform,
    typingTimer,
    storage;

  //   localStorage.setItem("simple", simple);
  //   localStorage.setItem("marc", marc);

  $("#btn-marc").click((e) => {
    e.preventDefault();
    $("#spinner").removeClass("d-none");
    let form = $("#form-body");
    let spinner = $("#spinner");
    let type = form.attr("data-type");
    //     spinner.removeClass("d-none");
    dataform = {
      mode: type,
      data: localStorage.getItem("data")
        ? JSON.parse(localStorage.getItem("data"))
        : [],
    };
    console.log(dataform);

    ajax = notformxhr({
      url: "/katalog/partial",
      data: dataform,
    }).done(function (response) {
      setTimeout(() => {
        form.html(response);
        switch (type) {
          case "simple":
            form.attr("data-type", "marc");
            $("#btn-marc").html("Tampilkan Sederhana");
            break;
          case "marc":
            form.attr("data-type", "simple");
            $("#btn-marc").html("Tampilkan MARC");
            break;

          default:
            break;
        }
        spinner.addClass("d-none");
      }, 1500);
    });
  });

  function storevalue() {
    clearTimeout(typingTimer);
    typingTimer = setTimeout((e) => {
      storage = [{}];
      storeWorkSheetValue();
      storeTitleValue();
      storePublisherValue();
      storeNotesValue();
      storeLocationValue();
      storeDetailValue();
      storeDetailSubjectValue();
      storeAuthorValue();
      storePhysicalDescriptionValue();
      localStorage.setItem("data", JSON.stringify(storage));
      console.log(JSON.parse(localStorage.getItem("data")));
      // $(".opt").select2({
      //   theme: "bootstrap",
      //   width: "element",
      //   placeholder: $(this).data("placeholder"),
      // });
    }, 1000);
  }

  //   ------ work sheet ------ //
  function storeWorkSheetValue() {
    var worksheet = $(`#worksheet`);
    obj = { worksheet: worksheet.val() };
    storage[0].worksheet = obj.worksheet;
  }
  $(document).on("change", "#worksheet", storevalue);
  //   ------ end of work sheet ------ //

  //   ------ title ------ //
  function storeTitleValue() {
    var title = $(`#title`);
    var childTitle = $(`#child-title`);
    var heldBy = $(`#heldby`);
    var previousTitle = $(`.previous-title`);
    var objTitle = {
      title: title.val(),
      childtitle: childTitle.val(),
      heldby: heldBy.val(),
      previoustitle: [],
    };
    previousTitle.each((i, obj) => {
      var elem = $(obj).val();
      objTitle.previoustitle.push(elem);
    });
    storage[0].title = objTitle;
  }

  $(document).on("keyup", "#title", storevalue);
  $(document).on("keyup", "#child-title", storevalue);
  $(document).on("keyup", "#heldby", storevalue);
  $(document).on("keyup", ".previous-title", storevalue);

  //   add previous title
  $(document).on("click", "#add-previous-title", () => {
    let PreviousTitleLength = $(".previous-title").length;

    let PreviousTitleHtml = `<div id="previous-title-${PreviousTitleLength}" class="form-group previoustitle">
                              <div class="form-group">
                                    <div class="input-group">
                                          <input type="text" class="form-control previous-title" name="previous-title[]" placeholder="Judul Sebelumnya">
                                          <div class="input-group-append">
                                                <button class="btn btn-sm btn-light remove-previous-title" data type="button" data-target="#previous-title-${PreviousTitleLength}"><i class="fa fa-minus m-1"></i></button>
                                          </div>
                                    </div>
                              </div>
      </div>`;

    $("#previous-title-append").append(PreviousTitleHtml);
  });
  //   remove previous title
  $(document).on("click", ".remove-previous-title", function () {
    let target = $(this).closest(".remove-previous-title").attr("data-target");
    $(target).remove();

    indexing($(".previoustitle"), "previous-title");
    storevalue();
  });
  //   ----- end of title ----- //

  //   ----- Author ------ //
  //   ----- additional author ----- //
  function storeAuthorValue() {
    var element = $(`.additional-author-wrapper`);
    var obj = { author: {}, additionalAuthor: [] };

    obj.author.select = $("#author-option").val();
    obj.author.input = $("#author-input").val();
    obj.author.radio = $("#author-radio").val();
    element.each((i, e) => {
      var elem = {
        select: $(e).find("select").val(),
        input: $(e).find(".additional-author-input").val(),
        radio: $(e).find(".additional-author-radio:checked").val(),
      };
      obj.additionalAuthor.push(elem);
      //     console.log($(`${elem} radio`).val());
    });
    storage[0].author = obj.author;
    storage[0].additionalAuthor = obj.additionalAuthor;

    indexing($(".additional-author-wrapper"), "additional-author");
  }

  $(document).on("change", ".author-option", storevalue);
  $(document).on("keyup", ".author-input", storevalue);
  $(document).on("change", ".author-radio", storevalue);
  $(document).on("change", ".additional-author-select", storevalue);
  $(document).on("keyup", ".additional-author-input", storevalue);
  $(document).on("change", ".additional-author-radio", storevalue);

  // on keydown, clear the countdown
  $("additional-author-input").on("keydown", function () {
    clearTimeout(typingTimer);
  });

  // add additional author
  $(document).on("click", "#add-additional-author", () => {
    let AdditionalauthorLength = $(".additional-author-wrapper").length;
    //     AdditionalauthorLength++;

    let AdditionalauthorHtml = `
    <div id="additional-author-${AdditionalauthorLength}" class="additional-author-wrapper">
            <div class="row">
                  <div class="col-md-4">
                        <select name="additional-author[option][]" class="form-control opt additional-author-option" data-index="0">
                              <option value="1">Nama Orang</option>
                              <option value="2">Nama Badan</option>
                              <option value="3">Nama Pertemuan</option>
                        </select>
                  </div>
                  <div class="col-md-8">
                        <div class="input-group">
                              <input type="text" class="form-control additional-author-input" name="additional-author[input][]" placeholder="Tajuk Pengarang Tambahan" data-index="${AdditionalauthorLength}">
                              <div class="input-group-append">
                                    <button class="btn btn-sm btn-light remove-additional-author" type="button" data-target="#additional-author-${AdditionalauthorLength}"><i class="fa fa-minus m-1"></i></button>
                              </div>
                        </div>
                  </div>
            </div>
            <div class="input-radio p-2 bg-light">
                  <div class="row">
                        <div class="col-sm-4">
                              <div class="form-check">
                                    <label class="form-check-label">
                                          <input class="form-check-input additional-author-radio" type="radio" name="additional-author[radio][${AdditionalauthorLength}]" value="0" data-index="0" checked>
                                                Nama Depan
                                    </label>
                              </div>
                        </div>
                        <div class="col-sm-4">
                              <div class="form-check">
                                    <label class="form-check-label">
                                          <input class="form-check-input additional-author-radio" type="radio" name="additional-author[radio][${AdditionalauthorLength}]" value="1" data-index="0">
                                                Nama Belakang
                                    </label>
                              </div>
                        </div>
                        <div class="col-sm-4">
                              <div class="form-check">
                                    <label class="form-check-label">
                                          <input class="form-check-input additional-author-radio" type="radio" name="additional-author[radio][${AdditionalauthorLength}]" value="2" data-index="0">
                                                Nama Keluarga
                                    </label>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
    `;

    $("#addition-author-append").append(AdditionalauthorHtml);
    $(".additional-author-option").select2({
      width: "100%",
      placeholder: "Indikator",
      theme: "bootstrap",
    });
  });

  //   ------ end of Author

  // remove additional author
  $(document).on("click", ".remove-additional-author", function () {
    let target = $(this)
      .closest(".remove-additional-author")
      .attr("data-target");
    $(target).remove();
    storevalue();
  });

  /// ---------- end of additional author ---------- ///

  //   ----- Publisher ------ ///
  function storePublisherValue() {
    storage[0].place = $("#place").val();
    storage[0].name = $("#name").val();
    storage[0].year = $("#year").val();
    storage[0].currentfreq = $("#current-frequency").val();
    storage[0].previousfreq = $("#previous-frequency").val();
  }

  $(document).on("keyup", ".publisher", storevalue);

  // ------ end of Publisher ------ ///

  //   ----- Physical Description ------ ///
  function storePhysicalDescriptionValue() {
    storage[0].page = $("#page").val();
    storage[0].illustration = $("#illustration").val();
    storage[0].dimention = $("#dimention").val();
    storage[0].accompanyingmaterial = $("#accompanying-material").val();
  }

  $(document).on("keyup", ".description", storevalue);

  // ------ end of Physical Description ------ ///

  //   ----- Detail Subject ----- ///
  function storeDetailSubjectValue() {
    var obj = { subject: [], ddc: "", callnumber: [], issn: [] };

    storage[0].ddc = $("#class-ddc").val();
    var subject = $(`.subject-input`);
    subject.each((i, e) => {
      var elem = {
        select: $(e).find("select").val(),
        input: $(e).find("input").val(),
      };
      obj.subject.push(elem);
      //     console.log($(`${elem} radio`).val());
    });
    storage[0].subject = obj.subject;
    var callnumber = $(`.callnumber`);
    callnumber.each((i, e) => {
      var elem = $(e).val();
      obj.callnumber.push(elem);
      //     console.log($(`${elem} radio`).val());
    });
    storage[0].callnumber = obj.callnumber;
    var issn = $(`.issn`);
    issn.each((i, e) => {
      var elem = $(e).val();
      obj.issn.push(elem);
      //     console.log($(`${elem} radio`).val());
    });
    storage[0].issn = obj.issn;

    // localStorage.setItem("detailSubject", JSON.stringify(obj));
    // console.log(JSON.parse(localStorage.getItem("detailSubject")));
  }

  $(document).on("change", ".dropdown-subject", storevalue);
  $(document).on("keyup", "#class-ddc", storevalue);
  $(document).on("keyup", ".subject", storevalue);
  $(document).on("keyup", ".issn", storevalue);
  $(document).on("keyup", ".callnumber", storevalue);
  //   add subject
  $(document).on("click", "#add-subject", () => {
    let SubjectLength = $(".subject-input").length;
    SubjectLength++;

    let SubjectHtml =
      '<div id="subject-input-' +
      SubjectLength +
      '" class="form-group subject-input"><div class="row"><div class="col-md-2"><select name="subject[tag][]" class="form-control dropdown-subject"><option></option><option value="1">Value</option><option value="2">Value1</option><option value="3">Value2</option><option value="4">Value3</option></select></div><div class="col-md-10 input-group"><input type="text" class="form-control subject" name="subject[desc][]"><div class="input-group-append"><button class="btn btn-sm btn-light remove-subject" data-target="#subject-input-' +
      SubjectLength +
      '" type="button"><i class="fa fa-minus m-1"></i></button></div></div></div></div>';

    $("#subject-append").append(SubjectHtml);
    $(".dropdown-subject").select2({
      width: "100%",
      placeholder: "Subjek",
      theme: "bootstrap",
    });
    // alert(i);
  });

  //   remove subject
  $(document).on("click", ".remove-subject", function () {
    let target = $(this).closest(".remove-subject").attr("data-target");
    $(target).remove();

    storevalue();
  });

  //   add call number
  $("#callnumber").click(() => {
    let CallNumberLength = $("#call-number-append").data("total");
    CallNumberLength++;
    $("#call-number-append").data("total", CallNumberLength);

    let CallNumberHtml = `<div id="call-number-${CallNumberLength}" class="form-group">
                              <div class="form-group">
                                    <div class="input-group">
                                          <input type="text" class="form-control callnumber" name="callnumber[]" placeholder="No Panggil">
                                          <div class="input-group-append">
                                                <button id="remove-call-number" class="btn btn-sm btn-light remove-call-number" data type="button" data-target="#call-number-${CallNumberLength}"><i class="fa fa-minus m-1"></i></button>
                                          </div>
                                    </div>
                              </div>
      </div>`;

    $("#call-number-append").append(CallNumberHtml);
  });

  //   remove call number
  $(document).on("click", ".remove-call-number", function () {
    let target = $(this).closest(".remove-call-number").attr("data-target");
    $(target).remove();

    storevalue();
  });

  //   add issn
  $(document).on("click", "#add-issn", () => {
    let ISSNLength = $("#issn-append").data("total");
    ISSNLength++;
    $("#issn-append").data("total", ISSNLength);

    let ISSNHtml = `<div id="issn-${ISSNLength}" class="form-group">
                              <div class="form-group">
                                    <div class="input-group">
                                          <input type="text" class="form-control issn" name="issn[]" placeholder="ISSN">
                                          <div class="input-group-append">
                                                <button id="remove-issn" class="btn btn-sm btn-light remove-issn" data type="button" data-target="#issn-${ISSNLength}"><i class="fa fa-minus m-1"></i></button>
                                          </div>
                                    </div>
                              </div>
      </div>`;

    $("#issn-append").append(ISSNHtml);
  });

  //   remove issn
  $(document).on("click", ".remove-issn", function () {
    let target = $(this).closest(".remove-issn").attr("data-target");
    $(target).remove();

    storevalue();
  });

  //   ----- end of Detail Subject ------ ///

  //   ----- Note ----- ///
  function storeNotesValue() {
    var obj = { notes: [] };

    var note = $(`.notes`);
    note.each((i, e) => {
      var elem = {
        radio: $(e).find(".note-radio:checked").val(),
        input: $(e).find("input").val(),
      };
      obj.notes.push(elem);
      //     console.log($(`${elem} radio`).val());
    });
    storage[0].notes = obj.notes;

    // localStorage.setItem("notes", JSON.stringify(obj));
    // console.log(JSON.parse(localStorage.getItem("notes")));
  }

  $(document).on("keyup", ".note", storevalue);
  $(document).on("change", ".note-radio", storevalue);

  //   add note
  $(document).on("click", "#add-note", () => {
    let noteLength = $(".notes").length;
    //     noteLength++;

    let noteHtml = `<div id="note-${noteLength}" class="form-group notes mb-0">
                                    <div class="input-group">
                                          <input type="text" class="form-control note" name="notes[input][]" placeholder="Catatan">
                                          <small id="note-feedback" class="text-danger"></small>
                                                <div class="input-group-append">
                                                      <button class="btn btn-sm btn-light remove-notes" type="button" data-target="#note-${noteLength}"><i class="fa fa-minus m-1"></i></button>
                                                </div>
                                    </div>
                                    <div class="input-radio p-2 bg-light">
                                          <div class="row">
                                                <div class="col-sm-4 py-1">
                                                      <div class="form-check">
                                                            <label class="form-check-label">
                                                                  <input class="form-check-input note-radio" type="radio" name="notes[radio][${noteLength}]" value="0" checked="">
                                                                  Abstrak/Anotasi
                                                            </label>
                                                      </div>
                                                </div>
                                                <div class="col-sm-4 py-1">
                                                      <div class="form-check">
                                                            <label class="form-check-label">
                                                                  <input class="form-check-input note-radio" type="radio" name="notes[radio][${noteLength}]" value="1">
                                                                        Catatan Disertasi
                                                            </label>
                                                      </div>
                                                 </div>
                                                <div class="col-sm-4 py-1">
                                                      <div class="form-check">
                                                            <label class="form-check-label">
                                                                  <input class="form-check-input note-radio" type="radio" name="notes[radio][${noteLength}]" value="2">
                                                                  Catatan Bibliografi
                                                            </label>
                                                      </div>
                                                </div>
                                                <div class="col-sm-4 py-1">
                                                      <div class="form-check">
                                                            <label class="form-check-label">
                                                                  <input class="form-check-input note-radio" type="radio" name="notes[radio][${noteLength}]" value="2">
                                                                        Rincian Isi
                                                            </label>
                                                      </div>
                                                </div>
                                                <div class="col-sm-4 py-1">
                                                      <div class="form-check">
                                                            <label class="form-check-label">
                                                                  <input class="form-check-input note-radio" type="radio" name="notes[radio][${noteLength}]" value="2">
                                                                  Catatan Umum
                                                            </label>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>`;

    $("#note-append").append(noteHtml);
  });

  //   remove note
  $(document).on("click", ".remove-notes", function () {
    let target = $(this).closest(".remove-notes").attr("data-target");
    $(target).remove();

    storevalue();
    indexing($(".notes"), "notes");
  });
  //   ------ end of Note ------ ///

  // ------ Detail ------ ///
  function storeDetailValue() {
    var obj = {};

    obj.lang = $(`select[name="opt-language"]`).val();
    obj.paper = $(`select[name="paper-form"]`).val();
    obj.target = $(`select[name="target-group"]`).val();
    storage[0].lang = obj.lang;
    storage[0].paper = obj.paper;
    storage[0].target = obj.target;

    // localStorage.setItem("detail", JSON.stringify(obj));
    // console.log(JSON.parse(localStorage.getItem("detail")));
  }

  $(document).on("change", ".detail", storevalue);
  // ------ end of Detail ------ ///

  // ------ Location ------ ///
  function storeLocationValue() {
    var obj = { location: [] };

    var location = $(`.location`);
    location.each((i, e) => {
      var elem = $(e).val();
      obj.location.push(elem);
      //     console.log($(`${elem} radio`).val());
    });
    storage[0].location = obj.location;

    // localStorage.setItem("location", JSON.stringify(obj));
    // console.log(JSON.parse(localStorage.getItem("location")));
  }

  $(document).on("keyup", ".location", storevalue);

  //   add location
  $(document).on("click", "#add-location", () => {
    let locationLength = $("#location-append").data("total");
    locationLength++;
    $("#location-append").data("total", locationLength);

    let locationHtml = `<div id="location-${locationLength}" class="form-group">
                                    <div class="input-group">
                                          <input type="text" class="form-control location" name="location[]" placeholder="Lokasi Koneksi Daring">
                                          <div class="input-group-append">
                                                <button id="remove-location" class="btn btn-sm btn-light remove-location" data type="button" data-target="#location-${locationLength}"><i class="fa fa-minus m-1"></i></button>
                                          </div>
                                    </div>
      </div>`;

    $("#location-append").append(locationHtml);
  });

  //   remove location
  $(document).on("click", ".remove-location", function () {
    let target = $(this).closest(".remove-location").attr("data-target");
    $(target).remove();

    storevalue();
  });
  //   ------ end of Location ------ ////

  //submit form
  formapp.on("submit", (e) => {
    e.preventDefault();

    formData = new FormData(formapp[0]);
    ajax = xhr({
      url: "/katalog-aacr/create/action",
      data: formData,
    });

    ajax.done(function (res) {
      console.log(res);
      console.log("submit");
    });
  });
  //   end of submit form

  //   method / callback
  function xhr(params) {
    return $.ajax({
      type: "POST",
      url: window.location.origin + params.url,
      data: params.data,
      processData: false,
      contentType: false,
      dataType: "JSON",
    });
  }

  function notformxhr(params) {
    return $.ajax({
      type: "POST",
      url: window.location.origin + params.url,
      data: params.data,
      // contentType: "application/json; charset=utf-8",
      dataType: "html",
      // success: function (result) {
      //   console.log(result);
      //   $("#form-body").html(result);
      // },
    });
  }

  function indexing(element, name) {
    element.each((i, elem) => {
      $(elem).attr("id", `${name}-${i}`);
      $(elem).find(`.remove-${name}`).attr("data-target", `#${name}-${i}`);
      // $(elem).find("select").attr("name", `${name}[${i}][select]`);
      // $(elem).find('input[type="text"]').attr("name", `${name}[${i}][input]`);
      $(elem).find('input[type="radio"]').attr("name", `${name}[radio][${i}]`);
    });
  }
});
