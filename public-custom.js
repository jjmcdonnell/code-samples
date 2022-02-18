/ * public custom * /

let UserInterfaceModel = require('./models/UserInterface');
let RUIModel = new UserInterfaceModel();
let UserInterfaceController = require('./controllers/UserInterface');
let RUIController = new UserInterfaceController(RUIModel);

RUIController.startX(RUIModel);

$("#submit-login").click(function (e) {
    e.preventDefault();
    if ($("#ajax-login").parsley().validate()) {
        RUIController.fetchLoginUri();
    }
});
$("#submit-search-button").click(function () {
    console.log("searching....");
    //$("#search__form").submit();
});

$("#registration-submit-button").click(function (e) {
    if ($("#registration").parsley().validate()) {
        $("#register-action-buttons").css("display", "none");
        $("#registration-spinner").css("display", "block");
    }
});