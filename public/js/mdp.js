document.addEventListener('DOMContentLoaded', function () {
    const $input = document.querySelector("#registration_form_plainPassword");
    const $progress = document.querySelector('#progressbar');
    const MIN_PASSWORD_LENGTH = 8;
    const MID_PASSWORD_LENGTH = 16;

    $input.addEventListener("input",function () {
        var password = this.value;
        var note = 0;

        if (password.length < MIN_PASSWORD_LENGTH) {
            note += 0;
        } else {
            if (password.length >= MIN_PASSWORD_LENGTH) {
                note += 1;
            }
            if (password.length >= MID_PASSWORD_LENGTH) {
                note += 1;
            }
            if (lowercase(password)) {
                note += 2;
            }
            if (uppercase(password)) {
                note += 2;
            }
            if (digit(password)) {
                note += 2;
            }
            if (special(password)) {
                note += 2;
            }
        }
        $progress.value = note;
    });
});

/**
 * Check the lowercase
 * @param {string} password
 */
function lowercase(password) {
    return password.match(/[a-z]/);
}

/**
 * Check the uppercase
 * @param {string} password
 */
function uppercase(password) {
    return password.match(/[A-Z]/);
}

/**
 * Check the digit
 * @param {string} password
 */
function digit(password) {
    return password.match(/[0-9]/);
}

/**
 * Check the specials
 * @param {string} password
 */
function special(password) {
    return password.match(/[\&\@\$\-\_\!\?\,\;\.\:\:\\\=\*\+]/);
}
