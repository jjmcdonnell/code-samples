module.exports = function UserInterfaceController() {
    this.Model = null;
    this.api_prefix = "";
    this.startX = function (RUIModel) {
        this.Model = RUIModel;
        this.fetchSuggestions(this);
    };
    this.fetchLoginUri = function () {
        $("#ajax-login-inputs").css("display", "none");
        $("#ajax-login-spinner").css("display", "block");
        $("#submit-login").css("display", "none");
        let token = $("input[name=_token]").val();
        let email = $("input[name=email]").val();
        let password = $("input[name=password]").val();
        let resource = this.api_prefix + "/ajax/login";
        try {
            $.post(resource, {_token: token, email: email, password: password}, function (response) {
                if (response.status) {
                    $("#ajax-login-spinner").css("display", "none");
                    $(function () {
                        $('#loginModal').modal('hide');
                        location.reload();
                    });
                } else {
                    $("#ajax-login-spinner").css("display", "none");
                    $("#ajax-login-inputs").css("display", "block");
                    $("#submit-login").css("display", "block");
                    $("#ajax-login-response").html(response.message);
                }
            });
        } catch (e) {
            $("#ajax-login-spinner").css("display", "none");
            $("#ajax-login-inputs").css("display", "block");
            $("#submit-login").css("display", "block");
            $("#ajax-login-response").html(e);
        }
    }
    this.autoComplete = function (inp, arr) {
        let currentFocus;
        inp.addEventListener("input", function (e) {
            let a, b, i, val = this.value;
            closeAllLists();
            if (!val) {
                return false;
            }
            currentFocus = -1;
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            this.parentNode.appendChild(a);
            for (i = 0; i < arr.length; i++) {
                if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                    b = document.createElement("DIV");
                    b.setAttribute('id', 'div_' + i);
                    b.setAttribute('class', 'autocomplete-result');
                    b.innerHTML += "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                    b.innerHTML += "<a class='ac-result-a' href='/search?query=" + arr[i] + "'>" + arr[i].substr(val.length) + "</a>";
                    b.innerHTML += "<input type='hidden' id=ac_inp_" + i + " value='" + arr[i] + "'>";
                    b.addEventListener("click", function (e) {
                        inp.value = this.getElementsByTagName("input")[0].value;
                        closeAllLists();
                        search(inp.value);
                    });
                    a.appendChild(b);
                }
            }
        });
        inp.addEventListener("keydown", function (e) {
            let x = document.getElementById(this.id + "autocomplete-list");
            if (x)
                x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
                currentFocus++;
                addActive(x);
            } else if (e.keyCode == 38) { //up
                currentFocus--;
                addActive(x);
            } else if (e.keyCode == 13) {
                e.preventDefault();
                if (currentFocus > -1) {
                    if (x)
                        x[currentFocus].click();
                }
            } else {
                closeAllLists();
            }
        });
        function addActive(x) {
            if (!x) {
                return false;
            }
            removeActive(x);
            if (currentFocus >= x.length) {
                currentFocus = 0;
            }
            if (currentFocus < 0) {
                currentFocus = (x.length - 1);
            }
            x[currentFocus].classList.add("autocomplete-active");
        }
        function removeActive(x) {
            for (let i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }
        function closeAllLists(elmnt) {
            let x = document.getElementsByClassName("autocomplete-items");
            for (let i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != inp) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        }
        function search(query) {
            closeAllLists();
            window.location = "/search?query=" + query;
        }
        document.addEventListener("click", function (e) {
            closeAllLists();
            console.log(e.target);
        });
    }
    this.fetchSuggestions = function (Controller) {
        let resource = "/api/suggestions/";
        fetch(resource, {
            credentials: 'include',
            method: 'GET'
        }).then(function (response) {
            return response.json();
        }).then(function (suggestions) {
            Controller.autoComplete(document.getElementById("search_query"), suggestions);
        }).catch(function (ex) {
            console.log(ex);
        });
    }

};