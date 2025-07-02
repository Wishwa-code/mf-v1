var prevVal = "";
function validateRequired(x) {
    if (document.getElementById(x).value === "") {
        document.getElementById(x).style.background = '#ffcccc';
        return false;
    } else {
        document.getElementById(x).style.background = '#ffffff';
        return true;
    }
}


function IsNumeric(e) {
    var keyCode = e.which ? e.which : e.keyCode
    var ret = ((keyCode >= 48 && keyCode <= 57));
    return ret;
}

function check_validate(arr, err) {
    arr.forEach(check);
    function check(item) {
        if (!validateRequired(item)) {
            validateRequired(item);
            err++;
        }
    }
    return err;

}

// CHECK DAYS RANGE 1 TO 30
function isInRange(input, value, err) {
    if (!validateRequired(input)) {
        validateRequired(input);
        err++;
    }

    else {
        if (value > 30 || value < 1) {
            err++;
        }

        return err;
    }
}

// CHECK TP NO LENGTH
function validateTp(input, value, err) {
    if (!validateRequired(input)) {
        err++;
    } else {
        if (value.length !== 10 || !(/^\d{10}$/.test(value))) {
            err++;
        }
    }
    return err;
}

var prevVal = "";

function decimalFormat(input) {
    for (let i = 0; i < input.length; i++) {
        prevVal = $(input[i]).val();

        $(input[i]).on('input', function (e) {

            var val = $(this).val();

            var userVal = val.replace(/,/g, ""); // remove commas

            var validValue = /^[0-9]{0,13}(\.[0-9]*)?$/.test(userVal);

            $("#userVal").text(userVal);
            $("#validValue").text(validValue);

            if (userVal !== "" && !validValue && e.keyCode !== 46 && e.keyCode !== 8) {
                $(this).val(prevVal);
            } else {
                prevVal = val;
            }
        });
    }
}

// GENERATE A RANDOM PASSWORD
$("#gen_password").click(function (e) {
    e.preventDefault();

    var length = 8;
    var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    var retVal = "";

    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }

    $("#password").val(retVal);
});

function currencyFormat(amount) {
    let formattedAmount = amount.toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
    // Remove the dollar sign
    return formattedAmount.replace(/^[$]/, '');
}


function validatePasswordStrength(password) {
    const minLength = 8;
    const regex = {
        upper: /[A-Z]/,
        lower: /[a-z]/,
        digit: /\d/,
        special: /[!@#\$%\^\&*\)\(+=._-]+/
    };
    let strength = 0;

    if (password.length >= minLength) strength++;
    if (regex.upper.test(password)) strength++;
    if (regex.lower.test(password)) strength++;
    if (regex.digit.test(password)) strength++;
    if (regex.special.test(password)) strength++;

    return strength;
}
