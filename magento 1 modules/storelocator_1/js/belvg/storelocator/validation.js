/**
 * @package Store Locator.
 * @author: A.A.Treitjak
 * @copyright: 2012 - 2013 BelVG.com
 */
if(Validation) {
    Validation.addAllThese([
        ['validate-no-space', 'Please enter without space.', function (v) {
            return Validation.get('IsEmpty').test(v) || !/\s/.test(v);
        }],
        ['validate-no-html-tags', 'HTML tags are not allowed', function(v) {
            return !/<(\/)?\w+/.test(v);
        }],
        ['validate-no-space-begin', 'Please enter no spaces in the beginning.', function (v) {
            return Validation.get('IsEmpty').test(v) || !/^\s/.test(v);
        }],
        ['validate-no-space-end', 'Please enter no spaces at the end.', function (v) {
            return Validation.get('IsEmpty').test(v) || !/\s$/.test(v);
        }],
        ['validate-no-space-begin-end', 'Please enter without space.', function (v) {
            return Validation.get('IsEmpty').test(v) || !/^\s|\s$/.test(v);
        }],
        ['validate-path', 'Please enter a valid URL Key. For example "example-page", "example-page.html".', function (v) {
            return Validation.get('IsEmpty').test(v) || /^[a-z0-9][a-z0-9_-]+(\.[a-z0-9_-]+)?$/.test(v)
        }],
        ['validate-clean-url-http', 'Please enter a valid URL. For example http://www.example.com', function (v) {
            return Validation.get('IsEmpty').test(v) || /^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se|nl|ei)$)(:(\d+))?\/?/i.test(v)
        }],
        ['validate-max-value', 'The values ​​should not be greater than 5000.', function (v, elm) {

            var reMax = 5000;
            var massValues = v.split(',');

            if (!massValues instanceof Array) {
                return false;
            }

            var result = true;

            massValues.each(function(item){

                var numValue = parseNumber(item, 10);

                if (isNaN(numValue)) {
                    result = false;
                    return false;
                }

                if (numValue > reMax) {
                    result = false;
                    return false;
                }
            });

            return result;
        }],
        ['validate-radius', 'Please use numbers only in this field.', function(v, elm) {
            if (Validation.get('IsEmpty').test(v)) {
                return true;
            }

            var massValues = v.split(',');
            if (!massValues instanceof Array) {
                return false;
            }

            var result = true;

            massValues.each(function(item){

                var testType = /^\s*-?\d*(\.\d*)?\s*$/.test(item);

                if (!testType) {
                    result = false;
                    return false;
                }

                var numValue = parseNumber(item, 10);

                if (isNaN(numValue)) {
                    result = false;
                    return false;
                }
            });

            return result;
        }],
    ]);
}