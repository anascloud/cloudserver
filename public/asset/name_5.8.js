$('body').on('change', '.verity_file', function () {

    var sl=$(this).attr("data-sl");
    var filename=$("#filename_"+sl).val();
    $("#filename_"+sl).val("");
    $("#show_file_"+sl).html("");
    var file = document.getElementById($(this).attr('id')).files[0];

    var formData = new FormData();
    formData.append('file', file);
    formData.append('filename', filename);
    $("#show_file_"+sl).html("uploading");
    $.ajax({
        url: base_url + 'index.php/verify/upload',
        type: 'POST',
        xhr: function () {
            myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', function (prog) {
                    var value = ~~((prog.loaded / prog.total) * 100);
                    $("#show_file_"+sl).html(value + "%");
                }, false);
            }
            return myXhr;
        },
        data: formData,
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
        complete: function (res) {
            var json;
            try {
                json = JSON.parse(res.responseText);
            } catch (e) {
                json = res.responseText;
            }
            var result = json.split('-');
            if (result[0] == 'Done') {
                $("#show_file_"+sl).html('<a class="pdfviewer  btn btn-danger" data-toggle="modal" data-target="#myModal" href="#" data-url="' + base_url + 'uploads/verify/' + result[1] + '" data-file-title="">Preview</a>');
                $("#filename_"+sl).val(result[1]);
            } else {
                alert(json);
                $("#show_file_"+sl).html("Failed");
                $("#filename_"+sl).val("");
            }
        }
    });
});




$('body').on('focus', '.name', function () {

    var sl = $(this).attr("data-sl");
    var exam = $("#exam_" + sl).val();
    var year = $("#year_" + sl).val();
    var roll_no = $("#roll_no_" + sl).val();
    var regno = $("#regno_" + sl).val();
    if (exam == "") {
        alert("Select Exam");
    } else if (year == "") {
        alert("Select Year");
    } else if (roll_no == "") {
        alert("Enter Roll No");
    } else if (regno == "") {
        alert("Enter Reg. No");
    } else {
        $(".loader-header").show();
        $.post(base_url + "index.php/verify/checkname", { exam: exam, year: year, roll_no: roll_no, regno: regno }, function (result) {
            resultobj = JSON.parse(result);
            if (resultobj.status == '0') {
                $("#year_" + sl).focus();
                alert(resultobj.msg);
            } else if (resultobj.status == '1') {

            } else if (resultobj.status == '2') {
                $("#name_" + sl).val("");
                $("#roll_no_" + sl).focus();
                alert(resultobj.msg);
            } else if (resultobj.status == '3') {
                $("#name_" + sl).val(resultobj.name);
            }
            $(".loader-header").hide();
        });
    }
});


function sendSMSnameage() {
    var date = $("#final_meeting_date").val();
    if (date == "") {
        alert("Select Meeting Date From Left Side");
    } else {
        $(".loader-header").show();
        $.post(base_url + "index.php/eoffice/sendSMSnameage", { date: date }, function (result) {
            if (result == "ok") {
            } else {
                alert(result);
            }
            $(".loader-header").hide();
        });
    }
}

function set_yes_general() {

    var date = $("#final_meeting_date").val();
    if (date == "") {
        alert("Select Meeting Date From Left Side");
    } else {
        if (confirm("Are you sure, all general application of date " + date + " will be Yes?")) {
            $(".loader-header").show();
            $.post(base_url + "index.php/eoffice/setYesGeneral", { date: date }, function (result) {
                if (result == "ok") {
                    window.location.href = "";
                } else {
                    alert(result);
                }
            });
        }
    }
}


function previewImage(input) {
    var id = $(input).attr("data-id");
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#preview_' + id).attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}


function add_row() {
    $("#table_1").append('<tr>' + $("#table_1 tr:last-child").html() + '</tr>');
    $("#table_1 tr:last-child td:first-child").html(parseInt($("#table_1 tr:last-child td:first-child").html()) + 1)

}

function delete_row(min) {
    var total = $("#table_1 tr").length;
    if (total > min + 1) {
        $("#table_1 tr:last-child").remove();
    }
}

function meetingDecision(type, id) {
    if (confirm("Are you sure, It will be " + type)) {
        $(".loader-header").show();
        $.post(base_url + "index.php/eoffice/meetingDecision", { id: id, type: type }, function (result) {
            if (result == "ok") {
                $(".row_" + id).hide();
            } else {
                alert(result);
            }
            $(".loader-header").hide();
        });
    }
}


function certificateMeetingDecision(id) {
    var m_status = $("#m_status_" + id).val();
    var m_decision = $("#m_decision_" + id).val();
    if (m_status != "") {
        if (confirm("Are you sure, It will be " + m_status)) {
            $(".loader-header").show();
            $.post(base_url + "index.php/eoffice/certificateMeetingDecision", { id: id, m_status: m_status, m_decision: m_decision }, function (result) {
                if (result != "ok") {
                    alert(result);
                }
                $(".loader-header").hide();
            });
        }
    } else {
        alert("Please Select Meeting Decision");
    }

}


$('body').on('click', '.eexam', function () {
    if (this.checked) {

    } else {

    }
});


$('body').on('click', '#show_all_comment', function () {
    $(".com-all").show();
    $("#show_all_comment").hide();
    $("#show_bookmark_comment").show();

});


$('body').on('click', '.eexam', function () {
    var exam = $(this).attr("data-exam");
    if (this.checked) {
        $("." + exam + "_group").slideDown();
    } else {
        $("." + exam + "_group").slideUp();
    }
});


$('body').on('change', '.member_control', function () {
    var data_class = $(this).attr('data-class');
    if (this.checked) {
        $(".select_" + data_class).attr('required', true);
        $(".text_" + data_class).attr('required', true);
        $(".text_" + data_class).removeAttr('readonly');
        $("#val_" + data_class).val("1");

    } else {
        $(".select_" + data_class).val("");
        $(".text_" + data_class).val("");
        $(".text_" + data_class).attr('readonly', true);
        $(".select_" + data_class).removeAttr('required');
        $(".text_" + data_class).removeAttr('required');
        $("#val_" + data_class).val("0");
    }
});

$('body').on('change', '.updateMemberStatus', function () {
    var id = $("#app_id").attr('data-app-id');
    var data_mid = $(this).attr('data-mid');
    var data_sl = $(this).attr('data-sl');
    if (this.checked) {
        status = "1";
    } else {
        status = "2";
    }
    $.post(base_url + "index.php/eoffice/updateMemberStatus", { id: id, data_mid: data_mid, data_sl: data_sl, status: status }, function (result) {
        if (result == "ok") {
            location.reload();
        } else {
            alert(result);
        }
    });
});

$('body').on('click', '#show_bookmark_comment', function () {
    $(".com-all").hide();
    $(".bookmark").show();
    $("#show_all_comment").show();
    $("#show_bookmark_comment").hide();
});

$('body').on('click', '#change-bookmark', function () {
    var id = $(this).attr("data-id");
    var status = $(this).attr("data-status");
    var obj = $(this);
    $.post(base_url + "index.php/eoffice/changebookmark", { id: id, status: status }, function (result) {
        if (result == "ok") {
            if (status == "1") {
                $(obj).attr("data-status", "0");
                $(obj).removeClass("btn-info");
                $(obj).addClass("btn-default");
                $(obj).html('Inactive');

            } else {
                $(obj).attr("data-status", "1");
                $(obj).removeClass("btn-default");
                $(obj).addClass("btn-info");
                $(obj).html('Active');
            }
        } else {
            alert(result);
        }
    });
});


$(".date_input").click(function () {
    var date = $(this).html();
    var title = $(this).attr('data-title');
    var action = $("#date_action").val();
    if (action == "0") {
        $("#date_from").val(date);
        $("#date_calculator").val(title + " " + date + " থেকে ");
        $("#date_action").val(1);
    } else if (action == "1") {
        var from_date = new Date($("#date_from").val() + " 00:00:00");
        var from_time = from_date.getTime();
        var to_date = new Date(date + " 00:00:00");
        var to_time = to_date.getTime();
        var one_day = 1000 * 60 * 60 * 24;
        var dir_time = to_time - from_time;
        dir_date = dir_time / one_day;
        $("#date_to").val(date);
        $("#date_calculator").val($("#date_calculator").val() + title + " " + date + " পর্যন্ত " + dir_date + " দিন।");
        $("#date_action").val(0);


    }

});


function an_id_change() {
    $("#form2").html("");
    var an_id = $("#an_id").val();
    if (an_id != "") {
        var data = { an_id: an_id };
        load_html(base_url + 'index.php/eservice/form2/js', '#form2', data);
    }
}


function document_id_change() {
    $("#form2").html("");
    var an_id = $("#an_id").val();
    if (an_id != "") {
        var data = { an_id: an_id };
        load_html(base_url + 'index.php/document/form2/js', '#form2', data);
    }
}


$('body').on('click', '#loaddocumentform3', function () {
    var an_id = $("#an_id").val();
    var exam = $("#exam").val();
    var year = $("#year").val();
    var roll = $("#roll").val();
    var reg = $("#reg").val();
    var data = { exam: exam, year: year, roll: roll, reg: reg, an_id: an_id };
    if (an_id == "") {
        alert("Please Select Application Name")
    } else if (exam == "") {
        alert("Please Select Exam")
    } else if (year == "") {
        alert("Please Select Year")
    } else if (roll == "") {
        alert("Please Fill Roll")
    } else if (reg == "") {
        alert("Please Fill Reg")
    } else {
        load_html(base_url + 'index.php/document/form3/js', '#form3', data);
    }
});


function ins_change() {
    $("#form3").html("");
}


/*
 $("#an_id").change(function () {
 alert("test");
 $("#form2").html("");
 var an_id = $("#an_id").val();
 if (an_id != "") {
 var data = {an_id: an_id};
 load_html(base_url + 'index.php/eservice/form2/js', '#form2', data);
 }
 });
 */
$("body").on("change", ".rowcon", function () {
    var rowcon = $(this).attr("data-row");
    if ($(this).is(':checked')) {
        $('.' + rowcon).prop('checked', true);
    } else {
        $('.' + rowcon).prop('checked', false);
    }
});

$("body").on("change", "#checkallid", function () {
    if ($(this).is(':checked')) {

        $('.idcheckbox').prop('checked', true);
    } else {
        $('.idcheckbox').prop('checked', false);
    }
});

$("body").on("keyup", ".number", function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$("body").on("keyup", ".bangla", function () {
    this.value = this.value.replace(/[0-9A-Za-z]/g, '');
});

$(".number").keyup(function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$("body").on("change", "#dept_id", function () {
    dept_id = $(this).val();
    $(".dept_all").hide();
    $("." + dept_id).show();
});


$("body").on("click", "#preview_name", function () {
    alert("Test");
    $("#pdfdownload").attr('href', "Test");
    $("#myModalLabel").html("Test");
    $("#myModalBody").html("Test");
});


$("body").on("click", ".pdfviewer", function () {
    url = $(this).attr("data-url");
    $("#pdfdownload").attr('href', url);
    $("#myModalLabel").html($(this).attr("data-file-title"));
    $("#myModalBody").html('<iframe src="https://docs.google.com/gview?url=https:' + url + '&embedded=true" style="width:800px; height:500px;" frameborder="0"></iframe>');
});

$(".app_catagory").change(function () {
    var catagory = $(this).val();
    var id = $(this).attr('data-app_id');
    $.post(base_url + "index.php/eoffice/setappcatagory", { catagory: catagory, id: id }, function (result) {
        alert(result);
    });
});


$("body").on("dblclick", "#preattach option", function () {
    $("#currentattach").append($(this).clone());
    $("#currentattach option").prop('selected', true);
    $(this).remove();
});

$("body").on("dblclick", "#currentattach option", function () {
    $("#preattach").append($(this).clone());
    $(this).remove();
});


$("body").on("dblclick", "#predate option", function () {
    $("#currentdate").append($(this).clone());
    $("#currentdate option").prop('selected', true);
    $(this).remove();
});

$("body").on("dblclick", "#currentdate option", function () {
    $("#predate").append($(this).clone());
    $(this).remove();
});

$("body").on("dblclick", "#preinput option", function () {
    $("#currentinput").append($(this).clone());
    $("#currentinput option").prop('selected', true);
    $(this).remove();
});

$("body").on("dblclick", "#currentinput option", function () {
    $("#preinput").append($(this).clone());
    $(this).remove();
});


$(document).on("change", "#a_status", function () {

    var a_status = $(this).val();
    $(".approve_group").hide();
    $(".showcause_group").hide();
    $(".visit_group").hide();
    if (a_status == "5") {
        $(".approve_group").show();
    } else if (a_status == "10") {
        $(".visit_group").show();
    } else if (a_status == "9") {
        $(".showcause_group").show();
    }


    if (a_status == "3" || a_status == "5" || a_status == "7" || a_status == "9") {
        $(".a_to_group").hide();
        $('#a_to').prop('required', false);
    } else {
        $(".a_to_group").show();
        $('#a_to').attr('required', true);
    }

    if (a_status == "7" || a_status == "9") {
        $(".form-group-duebill").show();
    } else {
        $(".form-group-duebill").hide();
    }

});


$('body').on('change', '.name_group', function () {
    if (this.checked) {
        if ($("#cdob").is(':checked')) {
            this.checked = false;
            alert("নাম পরিবর্তনের জন্য আরেকটি আবেদন করুন। একই আবেদনে জন্ম তারিখ এবং নাম সংশোধন করা যাবে না");
        }
    }
});

$('body').on('change', '.dob', function () {
    if (this.checked) {
        if (($("#cname").is(':checked')) || ($("#cfather").is(':checked')) || ($("#cmother").is(':checked')) || ($("#cgender").is(':checked')) || ($("#creligion").is(':checked'))) {
            this.checked = false;
            alert("জন্ম তারিখ পরিবর্তনের জন্য আরেকটি আবেদন করুন। একই আবেদনে জন্ম তারিখ এবং নাম সংশোধন করা যাবে না");
        }
    }
});

$('body').on('change', '.change_c_status', function () {
    if (this.checked) {
        var c_status = 0;
    } else {
        var c_status = 1;
    }
    var id = $(this).attr("data-id");
    $.post(base_url + 'index.php/eoffice/changecstatus', { c_status: c_status, id: id }, function (result) {
        if (result != 'ok') {
            alert(result);
        }
    });
});


$('body').on('change', '.set_priority_ssa', function () {
    if (this.checked) {
        var priority = 3;
    } else {
        var priority = 0;
    }
    var id = $(this).attr("data-id");
    $.post(base_url + 'index.php/eoffice/set_priority_ssa', { priority: priority, id: id }, function (result) {
        alert(result);
    });
});




$('body').on('blur', '#sonali', function () {
    $.post(base_url + 'index.php/general/check_sonali', { check_bit: $(this).val() }, function (result) {
        results = result.split("-");
        if (results[0] == "Done") {
            $("#sonali_amount").val(results[1]);
            $("#sonali").css("color", "green");
            $("#sonali").css("border-color", "green");
        } else {
            $("#sonali").css("color", "red");
            $("#sonali").css("border-color", "red");
            alert(result)
        }
    });
});

$('body').on('keydown', '#sonali', function () {
    $("#sonali_amount").val("");
});

$('body').on('change', '#cname', function () {
    $("#nname").attr("readonly", this.checked ? false : true);
    $("#nname").val("");
});

$('body').on('change', '#cfather', function () {
    $("#nfather").attr("readonly", this.checked ? false : true);
    $("#nfather").val("");
    if (this.checked) {
        $("#id137").prop('required', true);
        $(".attachment_137").css("display", "block");
        $(".attachment_138").css("display", "block");
    } else {
        $("#id137").prop('required', false);
        $(".attachment_137").css("display", "none");
        $(".attachment_138").css("display", "none");
    }
});

$('body').on('change', '#cmother', function () {
    $("#nmother").attr("readonly", this.checked ? false : true);
    $("#nmother").val("");
    if (this.checked) {
        $("#id136").prop('required', true);
        $(".attachment_136").css("display", "block");
        $(".attachment_139").css("display", "block");
    } else {
        $("#id136").prop('required', false);
        $(".attachment_136").css("display", "none");
        $(".attachment_139").css("display", "none");
    }
});


$('body').on('change', '#jscyear', function () {
    var jsc_year = $("#jscyear").val();
    if (jsc_year >= 2012) {
        $("#id115").prop('required', true);
        $(".attachment_115").css("display", "block");
    } else {
        $("#id115").prop('required', false);
        $(".attachment_115").css("display", "none");
    }
});
$('body').on('change', '#cgender', function () {
    $("#ngender").attr("disabled", this.checked ? false : true);
    $("#ngender").val("");
});

$('body').on('change', '#creligion', function () {
    $("#nreligion").attr("disabled", this.checked ? false : true);
    $("#nreligion").val("");
});

$('body').on('change', '#cdob', function () {
    $("#ndob").attr("disabled", this.checked ? false : true);
    $("#ndob").val("");
});

$('body').on('change', '#cjsc', function () {
    $("#jscyear").val("");
    $("#jscsess").val("");
    $("#jscroll").val("");
    $("#jscreg").val("");
    $("#jscccode").val("");
    $("#jscyear").attr("disabled", this.checked ? false : true);
    $("#jscsess").attr("disabled", this.checked ? false : true);
    $("#jscroll").attr("readonly", this.checked ? false : true);
    $("#jscreg").attr("readonly", this.checked ? false : true);
    $("#jscccode").attr("readonly", this.checked ? false : true);
});


$('body').on('change', '#cssc', function () {
    $("#sscyear").val("");
    $("#sscsess").val("");
    $("#sscroll").val("");
    $("#sscreg").val("");
    $("#sscccode").val("");
    $("#sscyear").attr("disabled", this.checked ? false : true);
    $("#sscsess").attr("disabled", this.checked ? false : true);
    $("#sscroll").attr("readonly", this.checked ? false : true);
    $("#sscreg").attr("readonly", this.checked ? false : true);
    $("#sscccode").attr("readonly", this.checked ? false : true);
});


$('body').on('change', '#chsc', function () {
    $("#hscyear").val("");
    $("#hscsess").val("");
    $("#hscroll").val("");
    $("#hscreg").val("");
    $("#hscccode").val("");
    $("#hscyear").attr("disabled", this.checked ? false : true);
    $("#hscsess").attr("disabled", this.checked ? false : true);
    $("#hscroll").attr("readonly", this.checked ? false : true);
    $("#hscreg").attr("readonly", this.checked ? false : true);
    $("#hscccode").attr("readonly", this.checked ? false : true);
});


$('body').on('change', '#cdibs', function () {
    $("#dibsyear").val("");
    $("#dibssess").val("");
    $("#dibsroll").val("");
    $("#dibsreg").val("");
    $("#dibsccode").val("");
    $("#dibsyear").attr("disabled", this.checked ? false : true);
    $("#dibssess").attr("disabled", this.checked ? false : true);
    $("#dibsroll").attr("readonly", this.checked ? false : true);
    $("#dibsreg").attr("readonly", this.checked ? false : true);
    $("#dibsccode").attr("readonly", this.checked ? false : true);
});


$('body').on('click', '.fileclick', function () {
    $("#" + $(this).attr('data-fileid')).click();
});

$('body').on('click', '#app_save', function () {
    $("#app_form").submit();
});

$("#exam").change(function () {
    $("#form2").html("");
});

$("#year").change(function () {
    $("#form2").html("");
});

$("#roll").keydown(function () {
    $("#form2").html("");
});

$("#reg").keydown(function () {
    $("#form2").html("");
});

$('body').on('click', '#eservice_form_save', function () {
    var total = $("#total_file").val();
    for (i = 1; i <= total; i++) {
        var file = '';
        file = $(".afile_" + i).val();
        if (file == "") {
            alert($(".afile_" + i).attr("data-file-title") + " সংযুক্ত করুন।");
            break;
        }
    }
    if (i > total) {
        if ($("#dataconfirm").is(':checked')) {
            $("#eservice_form_submit").click();
        } else {
            alert("উপরের শর্তটি পড়ে সম্মত হলে বামপাশ থেকে টিক দিন।");
        }
    }
});


$('body').on('click', '#certificate_form_save', function () {
    if ($("#ejsc").is(':checked') || $("#essc").is(':checked') || $("#ehsc").is(':checked')) {
        var total = $("#total_file").val();
        for (i = 1; i <= total; i++) {
            var file = '';
            file = $(".afile_" + i).val();
            var type = $(".afile_" + i).attr('data-type');
            if (file == "" && type == 0) {
                alert($(".afile_" + i).attr("data-file-title") + " সংযুক্ত করুন।");
                break;
            }
        }
        if (i > total) {
            if ($("#dataconfirm").is(':checked')) {
                $("#certificate_form_submit").click();
            } else {
                alert("উপরের শর্তটি পড়ে সম্মত হলে বামপাশ থেকে টিক দিন।");
            }
        }
    } else {
        // alert(" যে পরীক্ষার সমতুল্য সনদ প্রয়োজন সেটা নির্বাচন (টিক) করুন।  ");
    }
});


$('body').on('click', '#document_form_save', function () {
    var total = $("#total_file").val();
    for (i = 1; i <= total; i++) {
        var file = '';
        file = $(".afile_" + i).val();
        type = $(".afile_" + i).attr('data-type');
        if (file == "" && type == 0) {
            alert($(".afile_" + i).attr("data-file-title") + " সংযুক্ত করুন।");
            break;
        }
    }
    if (i > total) {
        if ($("#dataconfirm").is(':checked')) {
            $("#document_form_submit").click();
        } else {
            alert("উপরের শর্তটি পড়ে সম্মত হলে বামপাশ থেকে টিক দিন।");
        }
    }
});


$('body').on('keyup', '.upper', function () {
    $(this).val(this.value.toUpperCase());
});

$('body').on('keyup', '.regname', function () {
    var str = $(this).val();
    str = str.replace(/[^a-z A-Z-.]/g, '');
    str = str.toUpperCase();
    $(this).val(str);
});


$("#loadform2").click(function () {
    var exam = $("#exam").val();
    var year = $("#year").val();
    var roll = $("#roll").val();
    var reg = $("#reg").val();
    var data = { exam: exam, year: year, roll: roll, reg: reg };
    if (exam == "") {
        alert("Please Select Exam")
    } else if (year == "") {
        alert("Please Select Year")
    } else if (roll == "") {
        alert("Please Fill Roll")
    } else if (reg == "") {
        alert("Please Fill Reg")
    } else {
        load_html(base_url + 'index.php/name/form2/js', '#form2', data);
    }
});


function cancleApp(id) {
    var ceiin = 1;
    var cpass = 1;
    var an_id = $("#an_id").val();
    var eiin = $("#eiin").val();
    var pass = ''
    if (cpass == 1) {
        var pass = $("#password").val();
    }
    if (an_id != "") {
        var data = { id: id, an_id: an_id, eiin: eiin, pass: pass, ceiin: ceiin, cpass: cpass };

        $("#form3").html('<div class="loading"><img src="' + base_url + 'assets/custom/img/loading.gif"></div>');
        $.post(base_url + 'index.php/eservice/cancleApp/js', data, function (result) {
            if (result == "ok") {
                check_eiin_pass(ceiin, cpass)
            } else {
                $("#form3").html(result);
            }
        });

    }
}

function check_eiin_pass(ceiin, cpass) {
    var an_id = $("#an_id").val();
    var eiin = $("#eiin").val();
    var pass = ''
    if (cpass == 1) {
        var pass = $("#password").val();
    }
    if (an_id != "") {
        var data = { an_id: an_id, eiin: eiin, pass: pass, ceiin: ceiin, cpass: cpass };
        load_html(base_url + 'index.php/eservice/form3/js', '#form3', data);
    }
}

$("#last_update_temp").change(function () {
    $("#last_update").val($(this).val());
});

$("#add_in_comment").click(function () {
    $("#comment").val($("#comment").val() + $("#date_calculator").val());
});


function changeETC(url, id) {
    $.post(url, {}, function (result) {
        if (result) {
            if (result == "ok") {
                $(".row_" + id).hide();
            } else {
                alert(result);
            }
        }
    });
    return false;
}


function load_html(url, target, data) {
    $(".loader-header").show();
    $(target).html('');
    $.post(url, data, function (result) {
        if (result) {
            $(".loader-header").hide();
            $(target).html(result);
            setTimeout(function () {

                $(".datepicker").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd',
                    yearRange: "-220:+100",
                    minDate: '-220Y',
                    maxDate: '+100Y'
                });

            }, 500);

        }
    });
}


$("body").on("change", "#send_option", function () {
    if ($(this).val() == "1") {
        $(".app_checkbox").show()
    } else {
        $(".app_checkbox").hide()
    }
});

$("body").on("change", "#comment_temp", function () {
    $("#comment").val($("#comment").val() + $(this).val() + "। ");
});

$('body').on('change', '.file', function () {

    var file = document.getElementById($(this).attr('id')).files[0];
    var at_id = $(this).attr('data-at_id');
    var filetitle = $(this).attr('data-file-title');
    var view_id = "d" + at_id;
    var filename = $("#filename" + at_id).val();
    var temp_ap_id = $("#temp_ap_id").val();
    var temp_ap_type = $("#temp_ap_type").val();
    var formData = new FormData();
    formData.append('file', file);
    formData.append('at_id', at_id);
    formData.append('filename', filename);
    formData.append('temp_ap_id', temp_ap_id);
    formData.append('temp_ap_type', temp_ap_type);
    $("#" + view_id).html("uploading");
    $.ajax({
        url: base_url + 'index.php/attachment/upload',
        type: 'POST',
        xhr: function () {
            myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', function (prog) {
                    var value = ~~((prog.loaded / prog.total) * 100);
                    $("#" + view_id).html(value + "%");
                }, false);
            }
            return myXhr;
        },
        data: formData,
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
        complete: function (res) {
            var json;
            try {
                json = JSON.parse(res.responseText);
            } catch (e) {
                json = res.responseText;
            }
            var result = json.split('-');
            if (result[0] == 'Done') {
                var file_url = result[1];
                var file_ext = file_url.split(".");
                var len = file_ext.length;
                file_ext = file_ext[len - 1];
                file_ext = file_ext.toLowerCase();
                if (!(file_ext == 'pdf' || file_ext == 'doc' || file_ext == 'docx')) {
                    file_url += ".pdf";
                }
                $("#" + view_id).html('<a class="pdfviewer  btn btn-danger" data-toggle="modal" data-target="#myModal" href="#" data-url="' + base_url + 'attachment/' + file_url + '" data-file-title="' + filetitle + '">Preview</a>');
                $("#filename" + at_id).val(result[1]);
                $("[data-fileid=a" + at_id + "]").removeAttr("disabled");
                $("#id" + at_id).val(result[1]);
            } else {
                alert(json);
                $("#" + view_id).html("Failed");
                $("#filename" + at_id).val(filename);
            }
        }
    });
});

$('body').on('keyup', '.upper', function () {
    $(this).val(this.value.toUpperCase());
});

$('.print_div').click(function () {
    var result = $('#print').html();
    var popupWin = window.open('', '_blank', 'width=850,height=1100');
    popupWin.document.open();
    popupWin.document.write('<html><head><link rel="stylesheet" href="' + base_url + 'assets/custom/css/print.css"><link rel="stylesheet" href="' + base_url + 'assets/custom/css/font.css"><script type="text/javascript" src="' + base_url + 'assets/custom/js/print.js"></script></head><body onload="window_print()">' + result + '</body></html>');
    popupWin.document.close();
});

$('body').on('change', '#listtype', function () {
    var id = $(".data_table").attr("id");
    find_data(id, 1);
});


$(function () {


    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        yearRange: "-100:+100",
        minDate: '-100Y',
        maxDate: '+100Y'
    });
});


function check_amount(sonali) {
    $.post(base_url + "index.php/general/check_amount", { sonali: sonali }, function (result) {
        if (result == 'no') {
            alert("আপনার দেওয়া সোনালী নম্ববরটি সঠিক না।");
            $("#sonali_amount").val(0);
        } else {
            $("#sonali_amount").val(result);
        }
    });
}

function get_i_address(eiin) {
    $("#form3").html("");
    $.post(base_url + "index.php/general/get_i_address", { eiin: eiin }, function (result) {
        if (result == 'no') {
            alert("আপনার দেওয়া ই.আই.আই.এন. টি সঠিক না।");
            $("#i_address").val("");
        } else {
            $("#i_address").val(result);
            load_form3(eiin);
        }
    });
}


function check_password(password) {
    $("#password").val("");
    var eiin = $("#eiin").val();
    $.post(base_url + "index.php/general/check_password", { eiin: eiin, password: password }, function (result) {
        if (result != 'ok') {
            alert(result);
        } else {
            $("#password").val(password);
        }
    });
}

function change_catagory(from, to, id) {
    $.post(base_url + "index.php/eoffice/change_catagory", { from: from, to: to, id: id }, function (result) {
        if (result != 'ok') {
            alert(result);
        } else {
            $(".catagory_" + id + "_" + from).hide();
            $(".catagory_" + id + "_" + to).show();
        }
    });
}

function print_meetinglist() {
    var ptype = $("#ptype").val();
    var type = $("#type").val();
    var date = $("#date").val();
    if (type == "") {
        alert("Select a Application Type");
    } else if (date == "") {
        alert("Select a Meeting Date");
    } else {
        var win = window.open('//efile.dhakaeducationboard.gov.bd/index.php/eoffice/printmeetinglist/' + type + '/' + date + '/' + ptype, '_blank');
        if (win) {
            win.focus();
        } else {
            alert('Please allow popups for this website');
        }
    }
}


function print_certificatemeetinglist() {
    var ptype = $("#ptype").val();
    var date = $("#date").val();
    if (date == "") {
        alert("Select a Meeting Date");
    } else {
        var win = window.open('//efile.dhakaeducationboard.gov.bd/index.php/eoffice/printcertificatemeetinglist/' + date + '/' + ptype, '_blank');
        if (win) {
            win.focus();
        } else {
            alert('Please allow popups for this website');
        }
    }
}

function find_data(id, page) {
    var listtype = "";
    if ($('#listtype').length) {
        listtype = $("#listtype").val();
    }
    url = $("#" + id).attr('data-url') + "/" + page + "/TRUE/" + listtype;
    target = $("#" + id).attr('data-target');


    var data = new Object();
    $("table thead tr:nth-child(2) td").each(function () {

        child = $(this).children(':first')[0];
        type = child.nodeName.toLowerCase();
        if (type == "input" || type == "select") {
            field = $(child).attr("data-field");
            value = $(child).val();
            data[field] = value;
        }
    });
    load_html(url, target, data);

    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        yearRange: "-100:+100",
        minDate: '-100Y',
        maxDate: '+100Y'
    });

}