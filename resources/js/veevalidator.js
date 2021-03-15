import { extend } from 'vee-validate';
import { required, email, dimensions, image, min, max, confirmed, max_value, min_value, integer } from 'vee-validate/dist/rules';
import { setInteractionMode } from 'vee-validate';

setInteractionMode('lazy')

/**
 *
 */
extend('confirmed', {
    ...confirmed,
    message: 'Slaptažodžiai nesutampa'
});

/**
 *
 */
extend('integer', {
    ...integer,
    message: 'Netinkama reikšmė'
});

/**
 *
 */
extend('image', {
    ...image,
    message: 'Neteisingas failo formatas'
});

/**
 *
 */
extend('min', {
    ...min,
    message: 'Lauko reikšmė per trumpa'
});

/**
 *
 */
extend('max', {
    ...max,
    message: 'Lauko reikšmė per ilga'
});

/**
 *
 */
extend('max_value', {
    ...max_value,
    message: 'Lauko reikšmė per didelė'
});

/**
 *
 */
extend('min_value', {
    ...min_value,
    message: 'Lauko reikšmė per maža'
});

/**
 * Required field rule
 */
extend('required', {
    ...required,
    message: 'Privalomas laukas'
});

/**
 * Dimensions rule
 */
extend('dimensions', {
    ...dimensions,
    message: 'Nuotrauka neteisingo dydžio'
});

/**
 * Email rule
 */
extend('email', {
    ...email,
    message: 'Neteisingas el.pašo adresas'
});

/**
 * Is Phone rule
 */
extend('isPhone', {
    params: ['country_code'],
    message: 'Neteisingas telefono numeris',
    validate(value, { country_code }) {

        const phones = {
            'lt': /^(\+370)\d{8}$/,
        };

        // Remove all possible spaces
        let phone = value.replace(/\s/g, '');

        return phones[country_code].test(phone);
    }
});

/**
 * Is Url rule
 */
extend('isUrl', {
    message: 'Neteisinga nuoroda',
    validate(value) {

        const url = /^(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/

        return url.test(value);
    }
});

/**
 * Max nr of files
 */
extend('maxFiles', {
    params: ['number'],
    message: 'Pasirinkote failų daugiau nei yra leistinas skaičius',
    validate(value, { number }) {
        if (! number) {
            return false;
        }

        return value.length > number ? false : true
    }
});

/**
 * Max image dimensions
 */
extend('maxDimensions', {
    params: ['max_width', 'max_height'],
    message: 'Nuotrauka negali būti didesne nei {max_width}x{max_height}px',
    validate(files, {max_width, max_height}) {
        return new Promise(resolve => {
            var reader = new FileReader();
            reader.readAsDataURL(files[0]);
            reader.onload = function (e) {
                var image = new Image();

                image.src = e.target.result;
                image.onload = function () {
                    var height = this.height;
                    var width = this.width;

                    if (height <= Number(max_width) && width <= Number(max_height)) {
                        resolve(true)

                    } else {
                        resolve(false)
                    }
                }
            }
        });
    }
});
