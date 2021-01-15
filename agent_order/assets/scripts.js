/*!
 * JavaScript Cookie v2.1.2
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        var OldCookies = window.Cookies;
        var api = window.Cookies = factory();
        api.noConflict = function () {
            window.Cookies = OldCookies;
            return api;
        };
    }
}(function () {
    function extend() {
        var i = 0;
        var result = {};
        for (; i < arguments.length; i++) {
            var attributes = arguments[i];
            for (var key in attributes) {
                result[key] = attributes[key];
            }
        }
        return result;
    }

    function init(converter) {
        function api(key, value, attributes) {
            var result;
            if (typeof document === 'undefined') {
                return;
            }

            // Write

            if (arguments.length > 1) {
                attributes = extend({
                    path: '/'
                }, api.defaults, attributes);

                if (typeof attributes.expires === 'number') {
                    var expires = new Date();
                    expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
                    attributes.expires = expires;
                }

                try {
                    result = JSON.stringify(value);
                    if (/^[\{\[]/.test(result)) {
                        value = result;
                    }
                } catch (e) {
                }

                if (!converter.write) {
                    value = encodeURIComponent(String(value))
                        .replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
                } else {
                    value = converter.write(value, key);
                }

                key = encodeURIComponent(String(key));
                key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
                key = key.replace(/[\(\)]/g, escape);

                return (document.cookie = [
                    key, '=', value,
                    attributes.expires ? '; expires=' + attributes.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                    attributes.path ? '; path=' + attributes.path : '',
                    attributes.domain ? '; domain=' + attributes.domain : '',
                    attributes.secure ? '; secure' : ''
                ].join(''));
            }

            // Read

            if (!key) {
                result = {};
            }

            // To prevent the for loop in the first place assign an empty array
            // in case there are no cookies at all. Also prevents odd result when
            // calling "get()"
            var cookies = document.cookie ? document.cookie.split('; ') : [];
            var rdecode = /(%[0-9A-Z]{2})+/g;
            var i = 0;

            for (; i < cookies.length; i++) {
                var parts = cookies[i].split('=');
                var cookie = parts.slice(1).join('=');

                if (cookie.charAt(0) === '"') {
                    cookie = cookie.slice(1, -1);
                }

                try {
                    var name = parts[0].replace(rdecode, decodeURIComponent);
                    cookie = converter.read ?
                        converter.read(cookie, name) : converter(cookie, name) ||
                    cookie.replace(rdecode, decodeURIComponent);

                    if (this.json) {
                        try {
                            cookie = JSON.parse(cookie);
                        } catch (e) {
                        }
                    }

                    if (key === name) {
                        result = cookie;
                        break;
                    }

                    if (!key) {
                        result[name] = cookie;
                    }
                } catch (e) {
                }
            }

            return result;
        }

        api.set = api;
        api.get = function (key) {
            return api(key);
        };
        api.getJSON = function () {
            return api.apply({
                json: true
            }, [].slice.call(arguments));
        };
        api.defaults = {};

        api.remove = function (key, attributes) {
            api(key, '', extend(attributes, {
                expires: -1
            }));
        };

        api.withConverter = init;

        return api;
    }

    return init(function () {
    });
}));

/**
* @license Input Mask plugin for jquery
* http://github.com/RobinHerbots/jquery.inputmask
* Copyright (c) 2010 - 2012 Robin Herbots
* Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
* Version: 1.2.2
*/

(function ($) {
    if ($.fn.inputmask == undefined) {
        $.inputmask = {
            //options default
            defaults: {
                placeholder: "_",
                optionalmarker: {
                    start: "[",
                    end: "]"
                },
                escapeChar: "\\",
                mask: null,
                oncomplete: $.noop, //executes when the mask is complete
                onincomplete: $.noop, //executes when the mask is incomplete and focus is lost
                oncleared: $.noop, //executes when the mask is cleared
                repeat: 0, //repetitions of the mask
                greedy: true, //true: allocated buffer for the mask and repetitions - false: allocate only if needed
                autoUnmask: false, //automatically unmask when retrieving the value with $.fn.val or value if the browser supports __lookupGetter__ or getOwnPropertyDescriptor
                clearMaskOnLostFocus: true,
                insertMode: true, //insert the input or overwrite the input
                clearIncomplete: false, //clear the incomplete input on blur
                aliases: {}, //aliases definitions => see jquery.inputmask.extensions.js
                onKeyUp: $.noop, //override to implement autocomplete on certain keys for example
                onKeyDown: $.noop, //override to implement autocomplete on certain keys for example
                showMaskOnHover: true, //show the mask-placeholder when hovering the empty input
                //numeric basic properties
                numericInput: false, //numericInput input direction style (input shifts to the left while holding the caret position)
                radixPoint: ".", // | ","
                //numeric basic properties
                definitions: {
                    '9': {
                        validator: "[0-9]",
                        cardinality: 1
                    },
                    'a': {
                        validator: "[A-Za-z\u0410-\u044F\u0401\u0451]",
                        cardinality: 1
                    },
                    '*': {
                        validator: "[A-Za-z\u0410-\u044F\u0401\u04510-9]",
                        cardinality: 1
                    }
                },
                keyCode: {
                    ALT: 18, 
                    BACKSPACE: 8, 
                    CAPS_LOCK: 20, 
                    COMMA: 188, 
                    COMMAND: 91, 
                    COMMAND_LEFT: 91, 
                    COMMAND_RIGHT: 93, 
                    CONTROL: 17, 
                    DELETE: 46, 
                    DOWN: 40, 
                    END: 35, 
                    ENTER: 13, 
                    ESCAPE: 27, 
                    HOME: 36, 
                    INSERT: 45, 
                    LEFT: 37, 
                    MENU: 93, 
                    NUMPAD_ADD: 107, 
                    NUMPAD_DECIMAL: 110, 
                    NUMPAD_DIVIDE: 111, 
                    NUMPAD_ENTER: 108,
                    NUMPAD_MULTIPLY: 106, 
                    NUMPAD_SUBTRACT: 109, 
                    PAGE_DOWN: 34, 
                    PAGE_UP: 33, 
                    PERIOD: 190, 
                    RIGHT: 39, 
                    SHIFT: 16, 
                    SPACE: 32, 
                    TAB: 9, 
                    UP: 38, 
                    WINDOWS: 91
                },
                ignorables: [8, 9, 13, 16, 17, 18, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 46, 91, 93, 108]
            },
            val: $.fn.val //store the original jquery val function
        };

        $.fn.inputmask = function (fn, options) {
            var opts = $.extend(true, {}, $.inputmask.defaults, options);
            var pasteEvent = isInputEventSupported('paste') ? 'paste' : 'input';

            var iphone = navigator.userAgent.match(/iphone/i) != null;
            var android = navigator.userAgent.match(/android.*mobile safari.*/i) != null;
            if (android) {
                var browser = navigator.userAgent.match(/mobile safari.*/i);
                var version = parseInt(new RegExp(/[0-9]+/).exec(browser));
                android = version <= 533;
            }
            var caretposCorrection = null;

            if (typeof fn == "string") {
                switch (fn) {
                    case "mask":
                        //init buffer
                        var _buffer = getMaskTemplate();
                        var tests = getTestingChain();

                        return this.each(function () {
                            mask(this);
                        });
                        break;
                    case "unmaskedvalue":
                        var tests = this.data('inputmask')['tests'];
                        var _buffer = this.data('inputmask')['_buffer'];
                        opts.greedy = this.data('inputmask')['greedy'];
                        opts.repeat = this.data('inputmask')['repeat'];
                        opts.definitions = this.data('inputmask')['definitions'];
                        return unmaskedvalue(this);
                        break;
                    case "remove":
                        var tests, _buffer;
                        return this.each(function () {
                            var $input = $(this), input = this;
                            setTimeout(function () {
                                if ($input.data('inputmask')) {
                                    tests = $input.data('inputmask')['tests'];
                                    _buffer = $input.data('inputmask')['_buffer'];
                                    opts.greedy = $input.data('inputmask')['greedy'];
                                    opts.repeat = $input.data('inputmask')['repeat'];
                                    opts.definitions = $input.data('inputmask')['definitions'];
                                    //writeout the unmaskedvalue
                                    input._valueSet(unmaskedvalue($input, true));
                                    //clear data
                                    $input.removeData('inputmask');
                                    //unbind all events
                                    $input.unbind(".inputmask");
                                    $input.removeClass('focus.inputmask');
                                    //restore the value property
                                    var valueProperty;
                                    if (Object.getOwnPropertyDescriptor)
                                        valueProperty = Object.getOwnPropertyDescriptor(input, "value");
                                    if (valueProperty && valueProperty.get) {
                                        if (input._valueGet) {
                                            Object.defineProperty(input, "value", {
                                                get: input._valueGet,
                                                set: input._valueSet
                                            });
                                        }
                                    } else if (document.__lookupGetter__ && input.__lookupGetter__("value")) {
                                        if (input._valueGet) {
                                            input.__defineGetter__("value", input._valueGet);
                                            input.__defineSetter__("value", input._valueSet);
                                        }
                                    }
                                    delete input._valueGet;
                                    delete input._valueSet;
                                }
                            }, 0);
                        });
                        break;
                    case "getemptymask": //return the default (empty) mask value, usefull for setting the default value in validation
                        if (this.data('inputmask'))
                            return this.data('inputmask')['_buffer'].join('');
                        else return "";
                    case "hasMaskedValue": //check wheter the returned value is masked or not; currently only works reliable when using jquery.val fn to retrieve the value 
                        return this.data('inputmask') ? !this.data('inputmask')['autoUnmask'] : false;
                    default:
                        //check if the fn is an alias
                        if (!resolveAlias(fn)) {
                            //maybe fn is a mask so we try
                            //set mask
                            opts.mask = fn;
                        }
                        //init buffer
                        var _buffer = getMaskTemplate();
                        var tests = getTestingChain();

                        return this.each(function () {
                            mask(this);
                        });

                        break;
                }
            }
            if (typeof fn == "object") {
                opts = $.extend(true, {}, $.inputmask.defaults, fn);
                resolveAlias(opts.alias); //resolve aliases
                //init buffer
                var _buffer = getMaskTemplate();
                var tests = getTestingChain();

                return this.each(function () {
                    mask(this);
                });
            }

            //helper functions
            function isInputEventSupported(eventName) {
                var el = document.createElement('input'),
                eventName = 'on' + eventName,
                isSupported = (eventName in el);
                if (!isSupported) {
                    el.setAttribute(eventName, 'return;');
                    isSupported = typeof el[eventName] == 'function';
                }
                el = null;
                return isSupported;
            }

            function resolveAlias(aliasStr) {
                var aliasDefinition = opts.aliases[aliasStr];
                if (aliasDefinition) {
                    if (aliasDefinition.alias) resolveAlias(aliasDefinition.alias); //alias is another alias
                    $.extend(true, opts, aliasDefinition);  //merge alias definition in the options
                    $.extend(true, opts, options);  //reapply extra given options
                    return true;
                }
                return false;
            }

            function getMaskTemplate() {
                var escaped = false, outCount = 0;
                if (opts.mask.length == 1 && opts.greedy == false) {
                    opts.placeholder = "";
                } //hide placeholder with single non-greedy mask
                var singleMask = $.map(opts.mask.split(""), function (element, index) {
                    var outElem = [];
                    if (element == opts.escapeChar) {
                        escaped = true;
                    }
                    else if ((element != opts.optionalmarker.start && element != opts.optionalmarker.end) || escaped) {
                        var maskdef = opts.definitions[element];
                        if (maskdef && !escaped) {
                            for (var i = 0; i < maskdef.cardinality; i++) {
                                outElem.push(getPlaceHolder(outCount + i));
                            }
                        } else {
                            outElem.push(element);
                            escaped = false;
                        }
                        outCount += outElem.length;
                        return outElem;
                    }
                });

                //allocate repetitions
                var repeatedMask = singleMask.slice();
                for (var i = 1; i < opts.repeat && opts.greedy; i++) {
                    repeatedMask = repeatedMask.concat(singleMask.slice());
                }

                return repeatedMask;
            }

            //test definition => {fn: RegExp/function, cardinality: int, optionality: bool, newBlockMarker: bool, offset: int, casing: null/upper/lower, def: definitionSymbol}
            function getTestingChain() {
                var isOptional = false, escaped = false;
                var newBlockMarker = false; //indicates wheter the begin/ending of a block should be indicated

                return $.map(opts.mask.split(""), function (element, index) {
                    var outElem = [];

                    if (element == opts.escapeChar) {
                        escaped = true;
                    } else if (element == opts.optionalmarker.start && !escaped) {
                        isOptional = true;
                        newBlockMarker = true;
                    }
                    else if (element == opts.optionalmarker.end && !escaped) {
                        isOptional = false;
                        newBlockMarker = true;
                    }
                    else {
                        var maskdef = opts.definitions[element];
                        if (maskdef && !escaped) {
                            var prevalidators = maskdef["prevalidator"], prevalidatorsL = prevalidators ? prevalidators.length : 0;
                            for (var i = 1; i < maskdef.cardinality; i++) {
                                var prevalidator = prevalidatorsL >= i ? prevalidators[i - 1] : [], validator = prevalidator["validator"], cardinality = prevalidator["cardinality"];
                                outElem.push({
                                    fn: validator ? typeof validator == 'string' ? new RegExp(validator) : new function () {
                                        this.test = validator;
                                    } : new RegExp("."), 
                                    cardinality: cardinality ? cardinality : 1, 
                                    optionality: isOptional, 
                                    newBlockMarker: isOptional == true ? newBlockMarker : false, 
                                    offset: 0, 
                                    casing: maskdef["casing"], 
                                    def: element
                                });
                                if (isOptional == true) //reset newBlockMarker
                                    newBlockMarker = false;
                            }
                            outElem.push({
                                fn: maskdef.validator ? typeof maskdef.validator == 'string' ? new RegExp(maskdef.validator) : new function () {
                                    this.test = maskdef.validator;
                                } : new RegExp("."), 
                                cardinality: maskdef.cardinality, 
                                optionality: isOptional, 
                                newBlockMarker: newBlockMarker, 
                                offset: 0, 
                                casing: maskdef["casing"], 
                                def: element
                            });
                        } else {
                            outElem.push({
                                fn: null, 
                                cardinality: 0, 
                                optionality: isOptional, 
                                newBlockMarker: newBlockMarker, 
                                offset: 0, 
                                casing: null, 
                                def: element
                            });
                            escaped = false;
                        }
                        //reset newBlockMarker
                        newBlockMarker = false;
                        return outElem;
                    }
                });
            }

            function isValid(pos, c, buffer, strict) { //strict true ~ no correction or autofill
                if (pos < 0 || pos >= getMaskLength()) return false;
                var testPos = determineTestPosition(pos), loopend = c ? 1 : 0, chrs = '';
                for (var i = tests[testPos].cardinality; i > loopend; i--) {
                    chrs += getBufferElement(buffer, testPos - (i - 1));
                }

                if (c) {
                    chrs += c;
                }
                //return is false or a json object => { pos: ??, c: ??}
                return tests[testPos].fn != null ? tests[testPos].fn.test(chrs, buffer, pos, strict, opts) : false;
            }

            function isMask(pos) {
                var testPos = determineTestPosition(pos);
                var test = tests[testPos];

                return test != undefined ? test.fn : false;
            }

            function determineTestPosition(pos) {
                return pos % tests.length;
            }

            function getPlaceHolder(pos) {
                return opts.placeholder.charAt(pos % opts.placeholder.length);
            }

            function getMaskLength() {
                var calculatedLength = _buffer.length;
                if (!opts.greedy && opts.repeat > 1) {
                    calculatedLength += (_buffer.length * (opts.repeat - 1));
                }
                return calculatedLength;
            }

            //pos: from position
            function seekNext(buffer, pos) {
                var maskL = getMaskLength();
                if (pos >= maskL) return maskL;
                var position = pos;
                while (++position < maskL && !isMask(position)) { };
                return position;
            }
            //pos: from position
            function seekPrevious(buffer, pos) {
                var position = pos;
                if (position <= 0) return 0;

                while (--position > 0 && !isMask(position)) { };
                return position;
            }

            function setBufferElement(buffer, position, element) {
                //position = prepareBuffer(buffer, position);

                var test = tests[determineTestPosition(position)];
                var elem = element;
                if (elem != undefined) {
                    switch (test.casing) {
                        case "upper":
                            elem = element.toUpperCase();
                            break;
                        case "lower":
                            elem = element.toLowerCase();
                            break;
                    }
                }

                buffer[position] = elem;
            }
            function getBufferElement(buffer, position, autoPrepare) {
                if (autoPrepare) position = prepareBuffer(buffer, position);
                return buffer[position];
            }

            //needed to handle the non-greedy mask repetitions
            function prepareBuffer(buffer, position, isRTL) {
                var j;
                if (isRTL) {
                    while (position < 0 && buffer.length < getMaskLength()) {
                        j = _buffer.length - 1;
                        position = _buffer.length;
                        while (_buffer[j] !== undefined) {
                            buffer.unshift(_buffer[j--]);
                        }
                    }
                } else {
                    while (buffer[position] == undefined && buffer.length < getMaskLength()) {
                        j = 0;
                        while (_buffer[j] !== undefined) { //add a new buffer
                            buffer.push(_buffer[j++]);
                        }
                    }
                }

                return position;
            }

            function writeBuffer(input, buffer, caretPos) {
                input._valueSet(buffer.join(''));
                if (caretPos != undefined) {
                    if (android) {
                        setTimeout(function () {
                            caret(input, caretPos);
                        }, 100);
                    }
                    else caret(input, caretPos);
                }
            };
            function clearBuffer(buffer, start, end) {
                for (var i = start, maskL = getMaskLength(); i < end && i < maskL; i++) {
                    setBufferElement(buffer, i, getBufferElement(_buffer.slice(), i));
                }
            };

            function setReTargetPlaceHolder(buffer, pos) {
                var testPos = determineTestPosition(pos);
                setBufferElement(buffer, pos, getBufferElement(_buffer, testPos));
            }

            function checkVal(input, buffer, clearInvalid, skipRadixHandling) {
                var isRTL = $(input).data('inputmask')['isRTL'],
                inputValue = truncateInput(input._valueGet(), isRTL).split('');

                if (isRTL) { //align inputValue for RTL/numeric input
                    var maskL = getMaskLength();
                    var inputValueRev = inputValue.reverse();
                    inputValueRev.length = maskL;

                    for (var i = 0; i < maskL; i++) {
                        var targetPosition = determineTestPosition(maskL - (i + 1));
                        if (tests[targetPosition].fn == null && inputValueRev[i] != getBufferElement(_buffer, targetPosition)) {
                            inputValueRev.splice(i, 0, getBufferElement(_buffer, targetPosition));
                            inputValueRev.length = maskL;
                        } else {
                            inputValueRev[i] = inputValueRev[i] || getBufferElement(_buffer, targetPosition);
                        }
                    }
                    inputValue = inputValueRev.reverse();
                }
                clearBuffer(buffer, 0, buffer.length);
                buffer.length = _buffer.length;
                var lastMatch = -1, checkPosition = -1, np, maskL = getMaskLength(), ivl = inputValue.length, rtlMatch = ivl == 0 ? maskL : -1;
                for (var i = 0; i < ivl; i++) {
                    for (var pos = checkPosition + 1; pos < maskL; pos++) {
                        if (isMask(pos)) {
                            var c = inputValue[i];
                            if ((np = isValid(pos, c, buffer, !clearInvalid)) !== false) {
                                if (np !== true) {
                                    pos = np.pos || pos; //set new position from isValid
                                    c = np.c || c; //set new char from isValid
                                }
                                setBufferElement(buffer, pos, c);
                                lastMatch = checkPosition = pos;
                            } else {
                                setReTargetPlaceHolder(buffer, pos);
                                if (c == getPlaceHolder(pos)) {
                                    checkPosition = pos;
                                    rtlMatch = pos;
                                }
                            }
                            break;
                        } else {   //nonmask
                            setReTargetPlaceHolder(buffer, pos);
                            if (lastMatch == checkPosition) //once outsync the nonmask cannot be the lastmatch
                                lastMatch = pos;
                            checkPosition = pos;
                            if (inputValue[i] == getBufferElement(buffer, pos))
                                break;
                        }
                    }
                }
                //Truncate buffer when using non-greedy masks
                if (opts.greedy == false) {
                    var newBuffer = truncateInput(buffer.join(''), isRTL).split('');
                    while (buffer.length != newBuffer.length) {  //map changes into the original buffer
                        isRTL ? buffer.shift() : buffer.pop();
                    }
                }

                if (clearInvalid) {
                    writeBuffer(input, buffer);
                }
                return isRTL ? (opts.numericInput ? ($.inArray(opts.radixPoint, buffer) != -1 && skipRadixHandling !== true ? $.inArray(opts.radixPoint, buffer) : seekNext(buffer, maskL)) : seekNext(buffer, rtlMatch)) : seekNext(buffer, lastMatch);
            }

            function escapeRegex(str) {
                var specials = ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'];
                return str.replace(new RegExp('(\\' + specials.join('|\\') + ')', 'gim'), '\\$1');
            }
            function truncateInput(inputValue, rtl) {
                return rtl ? inputValue.replace(new RegExp("^(" + escapeRegex(_buffer.join('')) + ")*"), "") : inputValue.replace(new RegExp("(" + escapeRegex(_buffer.join('')) + ")*$"), "");
            }

            function clearOptionalTail(input, buffer) {
                checkVal(input, buffer, false);
                var tmpBuffer = buffer.slice();
                if ($(input).data('inputmask')['isRTL']) {
                    for (var pos = 0; pos <= tmpBuffer.length - 1; pos++) {
                        var testPos = determineTestPosition(pos);
                        if (tests[testPos].optionality) {
                            if (getPlaceHolder(pos) == buffer[pos] || !isMask(pos))
                                tmpBuffer.splice(0, 1);
                            else break;
                        } else break;
                    }
                } else {
                    for (var pos = tmpBuffer.length - 1; pos >= 0; pos--) {
                        var testPos = determineTestPosition(pos);
                        if (tests[testPos].optionality) {
                            if (getPlaceHolder(pos) == buffer[pos] || !isMask(pos))
                                tmpBuffer.pop();
                            else break;
                        } else break;
                    }
                }
                writeBuffer(input, tmpBuffer);
            }

            //functionality fn
            function unmaskedvalue($input, skipDatepickerCheck) {
                var input = $input[0];
                if (tests && (skipDatepickerCheck === true || !$input.hasClass('hasDatepicker'))) {
                    var buffer = _buffer.slice();
                    checkVal(input, buffer);
                    return $.map(buffer, function (element, index) {
                        return isMask(index) && element != getBufferElement(_buffer.slice(), index) ? element : null;
                    }).join('');
                }
                else {
                    return input._valueGet();
                }
            }

            function caret(input, begin, end) {
                var npt = input.jquery && input.length > 0 ? input[0] : input;
                if (typeof begin == 'number') {
                    end = (typeof end == 'number') ? end : begin;
                    if (opts.insertMode == false && begin == end) end++; //set visualization for insert/overwrite mode
                    if (npt.setSelectionRange) {
                        npt.setSelectionRange(begin, end);
                    } else if (npt.createTextRange) {
                        var range = npt.createTextRange();
                        range.collapse(true);
                        range.moveEnd('character', end);
                        range.moveStart('character', begin);
                        range.select();
                    }
                    npt.focus();
                    if (android && end != npt.selectionEnd) caretposCorrection = {
                        begin: begin, 
                        end: end
                    };
                } else {
                    var caretpos = android ? caretposCorrection : null, caretposCorrection = null;
                    if (caretpos == null) {
                        if (npt.setSelectionRange) {
                            begin = npt.selectionStart;
                            end = npt.selectionEnd;
                        } else if (document.selection && document.selection.createRange) {
                            var range = document.selection.createRange();
                            begin = 0 - range.duplicate().moveStart('character', -100000);
                            end = begin + range.text.length;
                        }
                        caretpos = {
                            begin: begin, 
                            end: end
                        };
                    }
                    return caretpos;
                }
            };

            function mask(el) {
                var $input = $(el);
                if (!$input.is(":input")) return;

                //correct greedy setting if needed
                opts.greedy = opts.greedy ? opts.greedy : opts.repeat == 0;

                //handle maxlength attribute
                var maxLength = $input.prop('maxLength');
                if (getMaskLength() > maxLength && maxLength > -1) { //FF sets no defined max length to -1 
                    if (maxLength < _buffer.length) _buffer.length = maxLength;
                    if (opts.greedy == false) {
                        opts.repeat = Math.round(maxLength / _buffer.length);
                    }
                }
                $input.prop('maxLength', getMaskLength() * 2);

                //store tests & original buffer in the input element - used to get the unmasked value
                $input.data('inputmask', {
                    'tests': tests,
                    '_buffer': _buffer,
                    'greedy': opts.greedy,
                    'repeat': opts.repeat,
                    'autoUnmask': opts.autoUnmask,
                    'definitions': opts.definitions,
                    'isRTL': false
                });

                patchValueProperty(el);

                //init vars
                var buffer = _buffer.slice(),
                undoBuffer = el._valueGet(),
                skipKeyPressEvent = false, //Safari 5.1.x - modal dialog fires keypress twice workaround
                ignorable = false,
                lastPosition = -1,
                firstMaskPos = seekNext(buffer, -1),
                lastMaskPos = seekPrevious(buffer, getMaskLength()),
                isRTL = false;
                if (el.dir == "rtl" || opts.numericInput) {
                    el.dir = "ltr"
                    $input.css("text-align", "right");
                    $input.removeAttr("dir");
                    var inputData = $input.data('inputmask');
                    inputData['isRTL'] = true;
                    $input.data('inputmask', inputData);
                    isRTL = true;
                }

                //unbind all events - to make sure that no other mask will interfere when re-masking
                $input.unbind(".inputmask");
                $input.removeClass('focus.inputmask');
                //bind events
                $input.bind("mouseenter.inputmask", function () {
                    var $input = $(this), input = this;
                    if (!$input.hasClass('focus.inputmask') && opts.showMaskOnHover) {
                        var nptL = input._valueGet().length;
                        if (nptL < buffer.length) {
                            if (nptL == 0)
                                buffer = _buffer.slice();
                            writeBuffer(input, buffer);
                        }
                    }
                }).bind("blur.inputmask", function () {
                    var $input = $(this), input = this, nptValue = input._valueGet();
                    $input.removeClass('focus.inputmask');
                    if (nptValue != undoBuffer) {
                        $input.change();
                    }
                    if (opts.clearMaskOnLostFocus) {
                        if (nptValue == _buffer.join(''))
                            input._valueSet('');
                        else { //clearout optional tail of the mask
                            clearOptionalTail(input, buffer);
                        }
                    }
                    if (!isComplete(input)) {
                        $input.trigger("incomplete");
                        if (opts.clearIncomplete) {
                            if (opts.clearMaskOnLostFocus)
                                input._valueSet('');
                            else {
                                buffer = _buffer.slice();
                                writeBuffer(input, buffer);
                            }
                        }
                    }
                }).bind("focus.inputmask", function () {
                    var $input = $(this), input = this;
                    if (!$input.hasClass('focus.inputmask') && !opts.showMaskOnHover) {
                        var nptL = input._valueGet().length;
                        if (nptL < buffer.length) {
                            if (nptL == 0)
                                buffer = _buffer.slice();
                            caret(input, checkVal(input, buffer, true));
                        }
                    }
                    $input.addClass('focus.inputmask');
                    undoBuffer = input._valueGet();
                }).bind("mouseleave.inputmask", function () {
                    var $input = $(this), input = this;
                    if (opts.clearMaskOnLostFocus) {
                        if (!$input.hasClass('focus.inputmask')) {
                            if (input._valueGet() == _buffer.join('') || input._valueGet() == '')
                                input._valueSet('');
                            else { //clearout optional tail of the mask
                                clearOptionalTail(input, buffer);
                            }
                        }
                    }
                }).bind("click.inputmask", function () {
                    var input = this;
                    setTimeout(function () {
                        var selectedCaret = caret(input);
                        if (selectedCaret.begin == selectedCaret.end) {
                            var clickPosition = selectedCaret.begin;
                            lastPosition = checkVal(input, buffer, false);
                            if (isRTL)
                                caret(input, clickPosition > lastPosition && (isValid(clickPosition, buffer[clickPosition], buffer, true) !== false || !isMask(clickPosition)) ? clickPosition : lastPosition);
                            else
                                caret(input, clickPosition < lastPosition && (isValid(clickPosition, buffer[clickPosition], buffer, true) !== false || !isMask(clickPosition)) ? clickPosition : lastPosition);
                        }
                    }, 0);
                }).bind('dblclick.inputmask', function () {
                    var input = this;
                    setTimeout(function () {
                        caret(input, 0, lastPosition);
                    }, 0);
                }).bind("keydown.inputmask", keydownEvent
                    ).bind("keypress.inputmask", keypressEvent
                    ).bind("keyup.inputmask", keyupEvent
                    ).bind(pasteEvent + ".inputmask, dragdrop.inputmask, drop.inputmask", function () {
                    var input = this;
                    setTimeout(function () {
                        caret(input, checkVal(input, buffer, true));
                    }, 0);
                }).bind('setvalue.inputmask', function () {
                    var input = this;
                    undoBuffer = input._valueGet();
                    checkVal(input, buffer, true);
                    if (input._valueGet() == _buffer.join(''))
                        input._valueSet('');
                }).bind('complete.inputmask', opts.oncomplete)
                .bind('incomplete.inputmask', opts.onincomplete)
                .bind('cleared.inputmask', opts.oncleared);

                //apply mask
                lastPosition = checkVal(el, buffer, true);

                // Wrap document.activeElement in a try/catch block since IE9 throw "Unspecified error" if document.activeElement is undefined when we are in an IFrame.
                var activeElement;
                try {
                    activeElement = document.activeElement;
                } catch (e) { }
                if (activeElement === el) { //position the caret when in focus
                    $input.addClass('focus.inputmask');
                    caret(el, lastPosition);
                } else if (opts.clearMaskOnLostFocus) {
                    if (el._valueGet() == _buffer.join('')) {
                        el._valueSet('');
                    } else {
                        clearOptionalTail(el, buffer);
                    }
                }

                installEventRuler(el);

                //private functions
                function isComplete(npt) {
                    var complete = true, nptValue = npt._valueGet(), ml = nptValue.length;
                    for (var i = 0; i < ml; i++) {
                        if (isMask(i) && nptValue.charAt(i) == getPlaceHolder(i)) {
                            complete = false;
                            break;
                        }
                    }
                    return complete;
                }


                function installEventRuler(npt) {
                    var events = $._data(npt).events;

                    $.each(events, function (eventType, eventHandlers) {
                        $(npt).bind(eventType + ".inputmask", function (event) {
                            if (this.readOnly || this.disabled) {
                                event.stopPropagation();
                                event.stopImmediatePropagation();
                                event.preventDefault();
                                return false;
                            }
                        });
                        //!! the bound handlers are executed in the order they where bound
                        //reorder the events
                        var ourHandler = eventHandlers[eventHandlers.length - 1];
                        for (var i = eventHandlers.length - 1; i > 0; i--) {
                            eventHandlers[i] = eventHandlers[i - 1];
                        }
                        eventHandlers[0] = ourHandler;
                    });
                }

                function patchValueProperty(npt) {
                    var valueProperty;
                    if (Object.getOwnPropertyDescriptor)
                        valueProperty = Object.getOwnPropertyDescriptor(npt, "value");
                    if (valueProperty && valueProperty.get) {
                        if (!npt._valueGet) {

                            npt._valueGet = valueProperty.get;
                            npt._valueSet = valueProperty.set;

                            Object.defineProperty(npt, "value", {
                                get: function () {
                                    var $self = $(this), inputData = $(this).data('inputmask');
                                    return inputData && inputData['autoUnmask'] ? $self.inputmask('unmaskedvalue') : this._valueGet() != inputData['_buffer'].join('') ? this._valueGet() : '';
                                },
                                set: function (value) {
                                    this._valueSet(value);
                                    $(this).triggerHandler('setvalue.inputmask');
                                }
                            });
                        }
                    } else if (document.__lookupGetter__ && npt.__lookupGetter__("value")) {
                        if (!npt._valueGet) {
                            npt._valueGet = npt.__lookupGetter__("value");
                            npt._valueSet = npt.__lookupSetter__("value");

                            npt.__defineGetter__("value", function () {
                                var $self = $(this), inputData = $(this).data('inputmask');
                                return inputData && inputData['autoUnmask'] ? $self.inputmask('unmaskedvalue') : this._valueGet() != inputData['_buffer'].join('') ? this._valueGet() : '';
                            });
                            npt.__defineSetter__("value", function (value) {
                                this._valueSet(value);
                                $(this).triggerHandler('setvalue.inputmask');
                            });
                        }
                    } else {
                        if (!npt._valueGet) {
                            npt._valueGet = function () {
                                return this.value;
                            }
                            npt._valueSet = function (value) {
                                this.value = value;
                            }
                        }
                        if ($.fn.val.inputmaskpatch != true) {
                            $.fn.val = function () {
                                if (arguments.length == 0) {
                                    var $self = $(this);
                                    if ($self.data('inputmask')) {
                                        if ($self.data('inputmask')['autoUnmask'])
                                            return $self.inputmask('unmaskedvalue');
                                        else {
                                            var result = $.inputmask.val.apply($self);
                                            return result != $self.data('inputmask')['_buffer'].join('') ? result : '';
                                        }
                                    } else return $.inputmask.val.apply($self);
                                } else {
                                    var args = arguments;
                                    return this.each(function () {
                                        var $self = $(this);
                                        var result = $.inputmask.val.apply($self, args);
                                        if ($self.data('inputmask')) $self.triggerHandler('setvalue.inputmask');
                                        return result;
                                    });
                                }
                            };
                            $.extend($.fn.val, {
                                inputmaskpatch: true
                            });
                        }
                    }
                }
                //shift chars to left from start to end and put c at end position if defined
                function shiftL(start, end, c) {
                    while (!isMask(start) && start - 1 >= 0) start--;
                    for (var i = start; i < end && i < getMaskLength(); i++) {
                        if (isMask(i)) {
                            setReTargetPlaceHolder(buffer, i);
                            var j = seekNext(buffer, i);
                            var p = getBufferElement(buffer, j);
                            if (p != getPlaceHolder(j)) {
                                if (j < getMaskLength() && isValid(i, p, buffer, true) !== false && tests[determineTestPosition(i)].def == tests[determineTestPosition(j)].def) {
                                    setBufferElement(buffer, i, getBufferElement(buffer, j));
                                    setReTargetPlaceHolder(buffer, j); //cleanup next position
                                } else {
                                    if (isMask(i))
                                        break;
                                }
                            } else if (c == undefined) break;
                        } else {
                            setReTargetPlaceHolder(buffer, i);
                        }
                    }
                    if (c != undefined)
                        setBufferElement(buffer, isRTL ? end : seekPrevious(buffer, end), c);

                    buffer = truncateInput(buffer.join(''), isRTL).split('');
                    if (buffer.length == 0) buffer = _buffer.slice();

                    return start; //return the used start position
                }
                function shiftR(start, end, c, full) { //full => behave like a push right ~ do not stop on placeholders
                    for (var i = start; i <= end && i < getMaskLength(); i++) {
                        if (isMask(i)) {
                            var t = getBufferElement(buffer, i);
                            setBufferElement(buffer, i, c);
                            if (t != getPlaceHolder(i)) {
                                var j = seekNext(buffer, i);
                                if (j < getMaskLength()) {
                                    if (isValid(j, t, buffer, true) !== false && tests[determineTestPosition(i)].def == tests[determineTestPosition(j)].def)
                                        c = t;
                                    else {
                                        if (isMask(j))
                                            break;
                                        else c = t;
                                    }
                                } else break;
                            } else if (full !== true) break;
                        } else
                            setReTargetPlaceHolder(buffer, i);
                    }
                    var lengthBefore = buffer.length;
                    buffer = truncateInput(buffer.join(''), isRTL).split('');
                    if (buffer.length == 0) buffer = _buffer.slice();

                    return end - (lengthBefore - buffer.length);  //return new start position
                };

                function keydownEvent(e) {
                    //Safari 5.1.x - modal dialog fires keypress twice workaround
                    skipKeyPressEvent = false;

                    var input = this, k = e.keyCode, pos = caret(input);

                    //set input direction according the position to the radixPoint
                    if (opts.numericInput) {
                        var nptStr = input._valueGet();
                        var radixPosition = nptStr.indexOf(opts.radixPoint);
                        if (radixPosition != -1) {
                            isRTL = pos.begin <= radixPosition || pos.end <= radixPosition;
                        }
                    }

                    //backspace, delete, and escape get special treatment
                    if (k == opts.keyCode.BACKSPACE || k == opts.keyCode.DELETE || (iphone && k == 127)) {//backspace/delete
                        var maskL = getMaskLength();
                        if (pos.begin == 0 && pos.end == maskL) {
                            buffer = _buffer.slice();
                            writeBuffer(input, buffer);
                            caret(input, checkVal(input, buffer, false));
                        } else if ((pos.end - pos.begin) > 1 || ((pos.end - pos.begin) == 1 && opts.insertMode)) {
                            clearBuffer(buffer, pos.begin, pos.end);
                            writeBuffer(input, buffer, isRTL ? checkVal(input, buffer, false) : pos.begin);
                        } else {
                            var beginPos = pos.begin - (k == opts.keyCode.DELETE ? 0 : 1);
                            if (beginPos < firstMaskPos && k == opts.keyCode.DELETE) {
                                beginPos = firstMaskPos;
                            }
                            if (beginPos >= firstMaskPos) {
                                if (opts.numericInput && opts.greedy && k == opts.keyCode.DELETE && buffer[beginPos] == opts.radixPoint) {
                                    beginPos = seekNext(buffer, beginPos);
                                    isRTL = false;
                                }
                                if (isRTL) {
                                    beginPos = shiftR(firstMaskPos, beginPos, getPlaceHolder(beginPos), true);
                                    beginPos = (opts.numericInput && opts.greedy && k == opts.keyCode.BACKSPACE && buffer[beginPos + 1] == opts.radixPoint) ? beginPos + 1 : seekNext(buffer, beginPos);
                                } else beginPos = shiftL(beginPos, maskL);
                                writeBuffer(input, buffer, beginPos);
                            }
                        }
                        if (input._valueGet() == _buffer.join(''))
                            $(input).trigger('cleared');

                        return false;
                    } else if (k == opts.keyCode.END || k == opts.keyCode.PAGE_DOWN) { //when END or PAGE_DOWN pressed set position at lastmatch
                        setTimeout(function () {
                            var caretPos = checkVal(input, buffer, false, true);
                            if (!opts.insertMode && caretPos == getMaskLength() && !e.shiftKey) caretPos--;
                            caret(input, e.shiftKey ? pos.begin : caretPos, caretPos);
                        }, 0);
                        return false;
                    } else if (k == opts.keyCode.HOME || k == opts.keyCode.PAGE_UP) {//Home or page_up
                        caret(input, 0, e.shiftKey ? pos.begin : 0);
                        return false;
                    }
                    else if (k == opts.keyCode.ESCAPE) {//escape
                        input._valueSet(undoBuffer);
                        caret(input, 0, checkVal(input, buffer));
                        return false;
                    } else if (k == opts.keyCode.INSERT) {//insert
                        opts.insertMode = !opts.insertMode;
                        caret(input, !opts.insertMode && pos.begin == getMaskLength() ? pos.begin - 1 : pos.begin);
                        return false;
                    } else if (e.ctrlKey && k == 88) {
                        setTimeout(function () {
                            caret(input, checkVal(input, buffer, true));
                        }, 0);
                    } else if (!opts.insertMode) { //overwritemode
                        if (k == opts.keyCode.RIGHT) {//right
                            var caretPos = pos.begin == pos.end ? pos.end + 1 : pos.end;
                            caretPos = caretPos < getMaskLength() ? caretPos : pos.end;
                            caret(input, e.shiftKey ? pos.begin : caretPos, e.shiftKey ? caretPos + 1 : caretPos);
                            return false;
                        } else if (k == opts.keyCode.LEFT) {//left
                            var caretPos = pos.begin - 1;
                            caretPos = caretPos > 0 ? caretPos : 0;
                            caret(input, caretPos, e.shiftKey ? pos.end : caretPos);
                            return false;
                        }
                    }

                    opts.onKeyDown.call(this, e, opts); //extra stuff to execute on keydown
                    ignorable = $.inArray(k, opts.ignorables) != -1;
                }

                function keypressEvent(e) {
                    //Safari 5.1.x - modal dialog fires keypress twice workaround
                    if (skipKeyPressEvent) return false;
                    skipKeyPressEvent = true;

                    var input = this, $input = $(input);

                    e = e || window.event;
                    var k = e.which || e.charCode || e.keyCode;

                    if (opts.numericInput && k == opts.radixPoint.charCodeAt(opts.radixPoint.length - 1)) {
                        var nptStr = input._valueGet();
                        var radixPosition = nptStr.indexOf(opts.radixPoint);
                        caret(input, seekNext(buffer, radixPosition != -1 ? radixPosition : getMaskLength()));
                    }

                    if (e.ctrlKey || e.altKey || e.metaKey || ignorable) {//Ignore
                        return true;
                    } else {
                        if (k) {
                            $input.trigger('input');

                            var pos = caret(input), c = String.fromCharCode(k), maskL = getMaskLength();
                            clearBuffer(buffer, pos.begin, pos.end);

                            if (isRTL) {
                                var p = opts.numericInput ? pos.end : seekPrevious(buffer, pos.end), np;
                                if ((np = isValid(p == maskL || getBufferElement(buffer, p) == opts.radixPoint ? seekPrevious(buffer, p) : p, c, buffer, false)) !== false) {
                                    if (np !== true) {
                                        p = np.pos || pos; //set new position from isValid
                                        c = np.c || c; //set new char from isValid
                                    }

                                    var firstUnmaskedPosition = firstMaskPos;
                                    if (opts.insertMode == true) {
                                        if (opts.greedy == true) {
                                            var bfrClone = buffer.slice();
                                            while (getBufferElement(bfrClone, firstUnmaskedPosition, true) != getPlaceHolder(firstUnmaskedPosition) && firstUnmaskedPosition <= p) {
                                                firstUnmaskedPosition = firstUnmaskedPosition == maskL ? (maskL + 1) : seekNext(buffer, firstUnmaskedPosition);
                                            }
                                        }

                                        if (firstUnmaskedPosition <= p && (opts.greedy || buffer.length < maskL)) {
                                            if (buffer[firstMaskPos] != getPlaceHolder(firstMaskPos) && buffer.length < maskL) {
                                                var offset = prepareBuffer(buffer, -1, isRTL);
                                                if (pos.end != 0) p = p + offset;
                                                maskL = buffer.length;
                                            }
                                            shiftL(firstUnmaskedPosition, opts.numericInput ? seekPrevious(buffer, p) : p, c);
                                        } else return false;
                                    } else setBufferElement(buffer, opts.numericInput ? seekPrevious(buffer, p) : p, c);
                                    writeBuffer(input, buffer, opts.numericInput && p == 0 ? seekNext(buffer, p) : p);
                                    setTimeout(function () { //timeout needed for IE
                                        if (isComplete(input))
                                            $input.trigger("complete");
                                    }, 0);
                                } else if (android) writeBuffer(input, buffer, pos.begin);
                            }
                            else {
                                var p = seekNext(buffer, pos.begin - 1), np;
                                prepareBuffer(buffer, p, isRTL);
                                if ((np = isValid(p, c, buffer, false)) !== false) {
                                    if (np !== true) {
                                        p = np.pos || p; //set new position from isValid
                                        c = np.c || c; //set new char from isValid
                                    }
                                    if (opts.insertMode == true) {
                                        var lastUnmaskedPosition = getMaskLength();
                                        var bfrClone = buffer.slice();
                                        while (getBufferElement(bfrClone, lastUnmaskedPosition, true) != getPlaceHolder(lastUnmaskedPosition) && lastUnmaskedPosition >= p) {
                                            lastUnmaskedPosition = lastUnmaskedPosition == 0 ? -1 : seekPrevious(buffer, lastUnmaskedPosition);
                                        }
                                        if (lastUnmaskedPosition >= p)
                                            shiftR(p, buffer.length, c);
                                        else return false;
                                    }
                                    else setBufferElement(buffer, p, c);
                                    var next = seekNext(buffer, p);
                                    writeBuffer(input, buffer, next);

                                    setTimeout(function () { //timeout needed for IE
                                        if (isComplete(input))
                                            $input.trigger("complete");
                                    }, 0);
                                } else if (android) writeBuffer(input, buffer, pos.begin);
                            }
                            return false;
                        }
                    }
                }

                function keyupEvent(e) {
                    var $input = $(this), input = this;
                    var k = e.keyCode;
                    opts.onKeyUp.call(this, e, opts); //extra stuff to execute on keyup
                    if (k == opts.keyCode.TAB && $input.hasClass('focus.inputmask') && input._valueGet().length == 0) {
                        buffer = _buffer.slice();
                        writeBuffer(input, buffer);
                        if (!isRTL) caret(input, 0);
                        undoBuffer = input._valueGet();
                    }
                }
            }
        };
    }
})(jQuery);
/*
 * jQuery.bind-first library v0.1
 * Copyright (c) 2012 Vladimir Zhuravlev
 * 
 * Released under MIT License
 * 
 * Date: Sun Jan 15 20:05:49 GST 2012
 **/(function(a){function e(b,c,e){var f=c.split(/\s+/);b.each(function(){for(var b=0;b<f.length;++b){var c=a.trim(f[b]).match(/[^\.]+/i)[0];d(a(this),c,e)}})}function d(a,d,e){var f=c(a),g=f[d];if(!b){var h=g.pop();g.splice(e?0:g.delegateCount||0,0,h)}else e?f.live.unshift(f.live.pop()):g.unshift(g.pop())}function c(c){return b?c.data("events"):a._data(c[0]).events}var b=parseFloat(a.fn.jquery)<1.7;a.fn.bindFirst=function(){var b=a(this),c=a.makeArray(arguments),d=c.shift();d&&(a.fn.bind.apply(b,arguments),e(b,d));return b},a.fn.delegateFirst=function(){var b=a(this),c=a.makeArray(arguments),d=c[1];d&&(c.splice(0,2),a.fn.delegate.apply(b,arguments),e(a(this),d,!0));return b},a.fn.liveFirst=function(){var b=a(this),c=a.makeArray(arguments);c.unshift(b.selector),a.fn.delegateFirst.apply(a(document),c);return b}})(jQuery)
;	 
/*
 * @license Multi Input Mask plugin for jquery
 * https://github.com/andr-04/inputmask-multi
 * Copyright (c) 2012 Andrey Egorov
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 * Version: 1.0.2
 *
 * Requriements:
 * https://github.com/RobinHerbots/jquery.inputmask
 * https://github.com/private-face/jquery.bind-first
 */
(function ($) {
    $.masksLoad = function(url) {
        var maskList;
        $.ajax({
            url: url,
            async: false,
            dataType: 'json',
            success: function (response) {
                maskList = response;
            }
        });
        return maskList;
    }

    $.masksSort = function(maskList, defs, match, key) {
        maskList.sort(function (a, b) {
            var ia = 0, ib = 0;
            for (; (ia<a[key].length && ib<b[key].length);) {
                var cha = a[key].charAt(ia);
                var chb = b[key].charAt(ib);
                if (!match.test(cha)) {
                    ia++;
                    continue;
                }
                if (!match.test(chb)) {
                    ib++;
                    continue;
                }
                if ($.inArray(cha, defs) != -1 && $.inArray(chb, defs) == -1) {
                    return 1;
                }
                if ($.inArray(cha, defs) == -1 && $.inArray(chb, defs) != -1) {
                    return -1;
                }
                if ($.inArray(cha, defs) == -1 && $.inArray(chb, defs) == -1) {
                    if (cha != chb) {
                        return cha < chb ? -1 : 1;
                    }
                }
                ia++;
                ib++;
            }
            for (; (ia<a[key].length || ib<b[key].length);) {
                if (ia<a[key].length && !match.test(a[key].charAt(ia))) {
                    ia++;
                    continue;
                }
                if (ib<b[key].length && !match.test(b[key].charAt(ib))) {
                    ib++;
                    continue;
                }
                if (ia<a[key].length) {
                    return 1;
                }
                if (ib<b[key].length) {
                    return -1;
                }
            }
            return 0;
        });
        return maskList;
    }

    $.fn.inputmasks = function(maskOpts, mode) {
        //Helper Functions
        var caret = function(begin, end) {
            if (typeof begin == 'number') {
                end = (typeof end == 'number') ? end : begin;
                if (this.setSelectionRange) {
                    this.setSelectionRange(begin, end);
                } else if (this.createTextRange) {
                    var range = this.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', end);
                    range.moveStart('character', begin);
                    range.select();
                }
            } else {
                if (this.setSelectionRange) {
                    begin = this.selectionStart;
                    end = this.selectionEnd;
                } else if (document.selection && document.selection.createRange) {
                    var range = document.selection.createRange();
                    begin = 0 - range.duplicate().moveStart('character', -100000);
                    end = begin + range.text.length;
                }
                return {
                    begin: begin,
                    end: end
                };
            }
        };

        var keys = Object.keys || function(obj) {
            if (obj !== Object(obj)) {
                throw new TypeError('Invalid object');
            }
            var keys = [];
            for (var key in obj) {
                keys[keys.length] = key;
            }
            return keys;
        };

        maskOpts = $.extend(true, {
            onMaskChange: $.noop
        }, maskOpts);
        var defs = {};
        for (var def in maskOpts.inputmask.definitions) {
            var validator = maskOpts.inputmask.definitions[def].validator;
            switch (typeof validator) {
                case "string":
                    defs[def] = new RegExp(validator);
                    break;
                case "object":
                    if ("test" in maskOpts.definitions[def].validator) {
                        defs[def] = validator;
                    }
                    break;
                case "function":
                    defs[def] = {
                        test: validator
                    };
                    break;
            }
        }
        maskOpts.inputmask.definitions[maskOpts.replace] = {
            validator: maskOpts.match.source,
            cardinality: 1
        };
        var iphone = navigator.userAgent.match(/iphone/i) != null;
        var oldmatch = false;
        var placeholder = $.extend(true, {}, $.inputmask.defaults, maskOpts.inputmask).placeholder;
        var insertMode = $.extend(true, {}, $.inputmask.defaults, maskOpts.inputmask).insertMode;

        var maskMatch = function(text) {
            var mtxt = "";
            for (var i=0; i<text.length; i++) {
                var ch = text.charAt(i);
                if (ch == placeholder) {
                    break;
                }
                if (maskOpts.match.test(ch)) {
                    mtxt += ch;
                }
            }
            for (var mid in maskOpts.list) {
                var mask = maskOpts.list[mid][maskOpts.listKey];
                var pass = true;
                for (var it=0, im=0; (it<mtxt.length && im<mask.length);) {
                    var chm = mask.charAt(im);
                    var cht = mtxt.charAt(it);
                    if (!maskOpts.match.test(chm) && !(chm in defs)) {
                        im++;
                        continue;
                    }
                    if (((chm in defs) && defs[chm].test(cht)) || (cht == chm)) {
                        it++;
                        im++;
                    } else {
                        pass = false;
                        break;
                    }
                }
                if (pass && it==mtxt.length) {
                    var determined = mask.substr(im).search(maskOpts.match) == -1;
                    mask = mask.replace(new RegExp([maskOpts.match.source].concat(keys(defs)).join('|'), 'g'), maskOpts.replace);
                    var completed = mask.substr(im).search(maskOpts.replace) == -1;
                    return {
                        mask: mask,
                        obj: maskOpts.list[mid],
                        determined: determined,
                        completed: completed
                    };
                }
            }
            return false;
        }

        var caretApply = function(oldMask, newMask, oldPos) {
            if (!oldMask) {
                return 0;
            }
            var pos = 0, startPos = 0;
            for (; pos < oldPos.begin; pos++) {
                if (oldMask.charAt(pos) == maskOpts.replace) {
                    startPos++;
                }
            }
            var endPos = 0;
            for (; pos < oldPos.end; pos++) {
                if (oldMask.charAt(pos) == maskOpts.replace) {
                    endPos++;
                }
            }
            for (pos = 0; (pos < newMask.length && (startPos > 0 || newMask.charAt(pos) != maskOpts.replace)); pos++) {
                if (newMask.charAt(pos) == maskOpts.replace) {
                    startPos--;
                }
            }
            startPos = pos;
            for (; (pos < newMask.length && endPos > 0); pos++) {
                if (newMask.charAt(pos) == maskOpts.replace) {
                    endPos--;
                }
            }
            endPos = pos;
            return {
                begin: startPos,
                end: endPos
            };
        }

        var maskUnbind = function() {
            $(this)
            .unbind("keypress.inputmask", masksKeyPress)
            .unbind("input.inputmask", masksPaste)
            .unbind("paste.inputmask", masksPaste)
            .unbind("dragdrop.inputmask", masksPaste)
            .unbind("drop.inputmask", masksPaste)
            .unbind("keydown.inputmask", masksKeyDown)
            .unbind("setvalue.inputmask", masksSetValue)
            .unbind("blur.inputmask", masksChange);
        }

        var maskRebind = function() {
            maskUnbind.call(this);
            $(this)
            .bindFirst("keypress.inputmask", masksKeyPress)
            .bindFirst("input.inputmask", masksPaste)
            .bindFirst("paste.inputmask", masksPaste)
            .bindFirst("dragdrop.inputmask", masksPaste)
            .bindFirst("drop.inputmask", masksPaste)
            .bindFirst("keydown.inputmask", masksKeyDown)
            .bindFirst("setvalue.inputmask", masksSetValue)
            .bind("blur.inputmask", masksChange);
        }

        var maskApply = function(match, newtext) {
            if (match && (newtext || match.mask != oldmatch.mask)) {
                var caretPos;
                if (!newtext) {
                    caretPos = caretApply(oldmatch.mask, match.mask, caret.call(this));
                }
                if (newtext) {
                    if (this._valueSet) {
                        this._valueSet(newtext);
                    } else {
                        this.value = newtext;
                    }
                }
                $(this).inputmask(match.mask, $.extend(true, maskOpts.inputmask, {
                    insertMode: insertMode
                }));
                if (!newtext) {
                    caret.call(this, caretPos.begin, caretPos.end);
                }
            }
            oldmatch = match;
            maskOpts.onMaskChange.call(this, match.obj, match.determined);
            return true;
        }

        var keyboardApply = function(e, text, insert) {
            var match = maskMatch(text);
            if (!match || match.obj != oldmatch.obj || match.determined != oldmatch.determined) {
                if (match) {
                    maskUnbind.call(this);
                    if (insert) {
                        maskApply.call(this, match);
                        $(this).trigger(e);
                    } else {
                        $(this).trigger(e);
                        maskApply.call(this, match);
                    }
                    maskRebind.call(this);
                }
                e.stopImmediatePropagation();
                return false;
            }
            return true;
        }

        var masksKeyDown = function(e) {
            e = e || window.event;
            var k = e.which || e.charCode || e.keyCode;
            if (k == 8 || k == 46 || (iphone && k == 127)) { // delete or backspace
                var text = this._valueGet();
                var caretPos = caret.call(this);
                if (caretPos.begin == caretPos.end || (!insertMode && caretPos.begin == caretPos.end-1)) {
                    var pos = caretPos.begin;
                    do {
                        if (k != 46) { // backspace
                            pos--;
                        }
                        var chr = text.charAt(pos);
                        text = text.substring(0, pos) + text.substring(pos+1);
                    } while (pos>0 && pos<text.length && chr != placeholder && !maskOpts.match.test(chr));
                } else {
                    var test = text.substring(0, caretPos.begin) + text.substring(caretPos.end);
                    if (test.search(maskOpts.match) == -1) {
                        text = test;
                    }
                }
                return keyboardApply.call(this, e, text, false);
            }
            if (k == 45) { // insert
                insertMode = !insertMode;
            }
            return true;
        }

        var masksKeyPress = function(e) {
            var text = this._valueGet();
            e = e || window.event;
            var k = e.which || e.charCode || e.keyCode, c = String.fromCharCode(k);
            caretPos = caret.call(this);
            text = text.substring(0, caretPos.begin) + c + text.substring(caretPos.end);
            return keyboardApply.call(this, e, text, true);
        }

        var masksChange = function(e) {
            var match = maskMatch(this._valueGet());
            maskApply.call(this, match);
            maskRebind.call(this);
            return true;
        }

        var masksSetValue = function(e) {
            maskInit.call(this);
            e.stopImmediatePropagation();
            return true;
        }

        var maskInit = function() {
            var text;
            if (this._valueGet) {
                text = this._valueGet();
            } else {
                text = this.value;
            }
            var match = maskMatch(text);
            while (!match && text.length>0) {
                text = text.substr(0, text.length-1);
                match = maskMatch(text);
            }
            maskApply.call(this, match, text);
            maskRebind.call(this);
        }

        var masksPaste = function(e) {
            var input = this;
            setTimeout(function() {
                maskInit.call(input);
            }, 0);
            e.stopImmediatePropagation();
            return true;
        }

        switch (mode) {
            case "isCompleted":
                var res = maskMatch((this[0]._valueGet && this[0]._valueGet()) || this[0].value);
                return (res && res.completed);
            default:
                this.each(function () {
                    maskInit.call(this);
                });
                return this;
        }
    }
})(jQuery);


$(function () {

    /*if (!Cookies.get('USER_CITY')) {
        $('#city_list').css('display', 'block');
    }*/
    $('body').on('click', '.select-city.pull-window a', function (event) {
        event.preventDefault();

        var old_element = '';
        var new_element = $(this).data('element');
        var object = 'city';
        var data = {'city-ajax': true, 'object': object, 'new': new_element, 'old': old_element, 'path': location.pathname + location.search};

        $.post('', data, function (response) {
            if (response.path) {
                if (response.js_cookie) {
                    var nd = (new Date(response.js_cookie.expire)).toUTCString();
                    document.cookie = response.js_cookie.value+";path="+response.js_cookie.path+";expires="+nd+";";
                }
                location.href = response.path;
            }
        }, 'json');
    });


    var maskList = [{ mask: "+7(###)###-##-##", cc: "RU", name_en: "Russia", desc_en: "", name_ru: "Россия", desc_ru: "" }];
	var maskOpts = {
		inputmask: {
			definitions: {
				'#': {
					validator: "[0-9]",
					cardinality: 1
				}
			},
			//clearIncomplete: true,
			showMaskOnHover: false,
			autoUnmask: true
		},
		match: /[0-9]/,
		replace: '#',
		list: maskList,
		listKey: "mask",
		onMaskChange: function (maskObj, determined) {
			if (determined) {
				var hint = maskObj.name_ru;
				if (maskObj.desc_ru && maskObj.desc_ru != "") {
					hint += " (" + maskObj.desc_ru + ")";
				}
				$(this).next('span').html(hint);
			}
			$(this).attr("placeholder", $(this).inputmask("getemptymask"));
		}
	};

	
    $("#input-user-phone").inputmasks(maskOpts).val('+7');

    $('body').on('click', '.sp6_tar .js-select-cont li', function() {
        $(this).parents('.sp6_tar').find('select')[0].dispatchEvent(new Event('change'));
    });

    $('body').on('click', '.js-select-a', function (event) {
        $(this).parent().find('.js-select-cont').toggle();
        event.stopPropagation();
    });

    $('body').on('click', '.js-select-cont li a', function (event) {
        var $parent = $(this).parents('.js-select-wrap').parent();
        var $select = $parent.find('.js-select');
        $select.find('option').each(function (i, item) {
            $(item).removeAttr('selected');
        });
        $selectOption = $select.find('[data-id="' + $(this).data('href') + '"]');
        $selectOption.prop('selected', true);
        $parent.find('.js-select-a').html($(this).html() + '<span></span>');
        $parent.find('.js-select-cont').hide();
        if ($(this).hasClass('order-later'))
            $parent.find('.sp6_inp_time').show();
        else
            $parent.find('.sp6_inp_time').hide().val('');

        $select[0].dispatchEvent(new Event('change'));
        event.stopPropagation();
    });

    $('body').on('click', '.sp6_opt .js-select-cont .cls-sel', function(event) {
        $(this).parents('.js-select-cont').hide();
    })


    $('body').on('click', '.sp6_opt .js-select-cont li label', function(event) {
        if (event.target != event.currentTarget) return;

        event.stopPropagation();

        var $parent = $(this).parents('.js-select-wrap').parent();
        var $ul = $parent.find('ul');
        var opt = [];
        var $checked = $('.checked_opt');
        $checked.empty();

        setTimeout(function() {
            $ul.find('[type=checkbox]').each(function(i, item) {
                if (!item.checked) return;
                opt.push($(item).parent().text());
            });
            opt.forEach(function(item){
                $checked.append('- '+item+'<br>');
            });
        });


        $ul[0].dispatchEvent(new Event('change'));
    });

    $('body').on('click', '.autocomplete_select', function(event){
        $(this).parent().find('input').val(event.target.innerText);
        $(this).parent().find('.active_autocomplete').removeClass('active_autocomplete');
        $('')
    });

    $(document).click(function(ev) {
        if (!$(ev.target).closest('.js-select-cont').length)
            $('.js-select-cont').hide();
    });










    if (0 && !!window.FileReader) {
        $('input[type="file"]').change(function (event) {
            var form = $(this).closest('form');
            if ($(form).hasClass('ajax_form')) {
                var input = event.target;
                $('#reader_' + input.name + '').remove();
                var reader = new FileReader();
                reader.onload = function () {
                    var image = new Image();
                    image.src = reader.result;
                    image.onload = function () {
                        if (image.width > 155) {
                            image.width = 155;
                            image.height = 75;
                        }
                        image.style.display = 'block';
                        $('<div id="reader_' + input.name + '"></div>').insertAfter($(input));
                        $(image).appendTo($('#reader_' + input.name + ''));
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        })
    }

    if (!!window.FormData) {
        $('body').on('click', 'input[type="submit"]', function () {
            var button = $(this);
            var form = $(this).closest('form');
            var result_div = $(form).closest('div');
            if ($(form).hasClass('ajax_form')) {
                $(this).addClass('loading_button');
                var fd = new FormData($(form)[0]);
                fd.append("iblock_submit", "true");
                fd.append("submit", "true");
                $.ajax({
                    url: $(form).attr('action'),
                    data: fd,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (data) {
                        $(result_div).find('.result_message').detach();
                        $(button).removeClass('loading_button');
                        var data = $.parseHTML(data);
                        var response_div = $(data).find('#' + $(result_div).attr('id'));
                        var response = $(response_div).find('.result_message');
                        if ($(response).find('.notetext').length || $(response).find('.mf-ok-text').length) {
                            $(form).html($(response).html());
                        }
                        else {
                            $(response).insertBefore($(form));
                        }
                        if ($(window).scrollTop() > $(result_div).offset().top) {
                            $('body, html').animate({scrollTop: $(result_div).offset().top}, 'fast');
                        }
                    }
                });
                return false;
            }

        })
    }

    /*if (!Cookies.get('USER_CITY')) {
        $('#city_list').css('display', 'block');
    }*/
    var urlCity = '/city3c/change_path.php'
    $('body').on('change', '#taxi_city', function () {
        var myCityId = $(this).find('option:selected').attr('data-id');
        $.post(urlCity, {cityID: myCityId, path: window.location.href}, function (data) {
            Cookies.set('CITY_USER', myCityId, {expires: 365, path: '/'});
            location.href = data.path;
        }, 'json');
    });

    $('body').on('click', '.pull-window a', function () {
        var myCityId = $(this).data('id');
        $.post(urlCity, {cityID: myCityId, path: window.location.href}, function (data) {
            Cookies.set('USER_CITY', myCityId, {expires: 365, path: '/'});
            location.href = data.path;
        }, 'json');
    })

    /*Mobile Menu show-hide*/
    function animateShowHideMenuMobail(_this) {
        function animateClose(_this) {
            _this.stop();
            _this.animate({'left': '0'}, 'slow', function () {
                _this.removeClass('open');
            });
        }

        function animateShow(_this) {
            _this.stop();
            _this.animate({'left': '240'}, 'slow', function () {
                _this.addClass('open');
            });
        }

        if (_this.hasClass('open')) {
            animateClose(_this);
        } else {
            animateShow(_this);
        }
    };

    $('.show-mobile-menu').click(function (event) {
        event.stopPropagation();
        event.preventDefault();
        animateShowHideMenuMobail($('.wrapper'));
    });

    $(window).resize(function () {
        if ($(window).width() > 719) {
            $('.wrapper').removeAttr('style').removeClass('open');
        }
    });

    /*Handmade Select with checkbox*/
    $('.select-wishes .selected-items, .select-wishes .select-arrow-wrap').click(function (event) {
        $('.select-wishes ul').toggle();
        event.stopPropagation();
    });

    $('.select-wishes ul').click(function (event) {
        event.stopPropagation();
    })

    $('.select-wishes .select-close').click(function () {
        $('.select-wishes ul').css('display', 'none');
    });

    $(document).click(function () {
        $('.select-wishes ul').css('display', 'none');
    });

    $('label').click(function () {
        selectWishes();
    });

    function selectWishes() {
        var arr = [];

        $('.select-wishes label').each(function (index, element) {

            if ($(this).find('input').is(':checked')) {
                arr.push($(element).text());
            }

        })

        if (arr.length == 0) {
            $('.select-wishes .selected-items').text('Выберите что нибудь');
        } else {
            $('.select-wishes .selected-items').text(arr.join(','));
        }

    }

    selectWishes();


    /*Show-hide order table*/
    $('.show-hide-table a').click(function () {
        showHideOrderTable($(this));
        return false;
    })

    function showHideOrderTable(_this) {
        function showOrderTable(_this) {
            _this.find('span.underline').text('Скрыть');
            _this.find('span.show-table').removeClass('show-table').addClass('hide-table');
            _this.parents('.my-order-footer').find('table.order-table').addClass('open-table').show();
        }

        function hideOrderTable(_this) {
            _this.find('span.underline').text('Подробности');
            _this.find('span.hide-table').removeClass('hide-table').addClass('show-table');
            _this.parents('.my-order-footer').find('table.order-table').removeClass('open-table').hide();
        }

        if (_this.parents('.my-order-footer').find('table.order-table').hasClass('open-table')) {
            hideOrderTable(_this);
        } else {
            showOrderTable(_this);
        }
    }

    /*Show-hide input*/
    $('label.time-disabled').click(function () {
        z();
    });

    function z() {
        if ($('#go-other:checked').size()) {
            $('#input-time').removeAttr('disabled');
        } else {
            $('#input-time').attr('disabled', true);
        }
    }

    z();

    /*Modal*/
    function openModalWindows() {
        var H = $("html"),
            W = $(window),
            D = $(document),
            F = function () {
                F.open.apply(this, arguments);
            };

        D.ready(function () {
            var w1, w2;

            if ($.scrollbarWidth === undefined) {
                // http://benalman.com/projects/jquery-misc-plugins/#scrollbarwidth
                $.scrollbarWidth = function () {
                    var parent = $('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo('body'),
                        child = parent.children(),
                        width = child.innerWidth() - child.height(99).innerWidth();
                    parent.remove();
                    return width;
                };
            }

            if ($.support.fixedPosition === undefined) {
                $.support.fixedPosition = (function () {
                    var elem = $('<div style="position:fixed;top:20px;"></div>').appendTo('body'),
                        fixed = (elem[0].offsetTop === 20 || elem[0].offsetTop === 15);
                    elem.remove();
                    return fixed;
                }());
            }

            $.extend(F.defaults, {
                scrollbarWidth: $.scrollbarWidth(),
                fixed: $.support.fixedPosition,
                parent: $('body')
            });

            //Get real width of page scroll-bar
            w1 = $(window).width();
            H.addClass('popup-lock-test');
            w2 = $(window).width();
            H.removeClass('popup-lock-test');
            $("<style type='text/css'>.popup-margin{margin-right:" + (w2 - w1) + "px;}</style>").appendTo("head");

        });

        var p = $('.popup-overlay');

        $('.open-popup').click(function () {
            id = $(this).attr('href');
            $(id).css('display', 'block');
            $('html').addClass('popup-lock popup-margin');
            return false
        })

        p.click(function (event) {
            e = event || window.event
            if (e.target == this) {
                $(p).css('display', 'none');
                $('html').removeClass('popup-lock popup-margin');
                return false
            }
        })

        $('.popup .close').click(function () {
            p.css('display', 'none');
            $('html').removeClass('popup-lock popup-margin');
            return false
        })
    }

    openModalWindows();

    /*Tabs*/
    $('ul.tabs-switches li').click(function () {
        if (!$(this).hasClass('active')) {
            var ul = $(this).parent();
            var prev_index = ul.find('.active').index();
            var cur_index = $(this).index();
            ul.find('li').eq(prev_index).removeClass('active');
            $(this).addClass('active');
            var box = ul.next('.tabs-wrap').children('.tabs-container');
            box.eq(prev_index).removeClass('visible');
            box.eq(cur_index).addClass('visible');
        }
        return false;
    });

    /*Stars*/

    $('.form_stars li').hover(function () {
        var nnum = $(this).data('num');
        for (var z = 1; z <= nnum; z++) {
            $('[data-num="' + z + '"]').find('span').addClass('act');
        }
    }, function () {
        $('.form_stars li').each(function () {
            $(this).find('span').removeClass('act');
        })
    })

    $('.form_stars li').on('click', function () {
        var nnum2 = $(this).data('num');
        $('#rating_value').attr('value', nnum2);
        $('.form_stars li').each(function () {
            $(this).find('span').removeClass('act2');
        })
        for (var z = 1; z <= nnum2; z++) {
            $('[data-num="' + z + '"]').find('span').addClass('act2');
        }
    })

    $('.pokaz_karty').on('click', function () {
        $(this).toggleClass('active');
        $('#map').toggleClass('active');
    })

});

/*
 *  Remodal - v0.6.3
 *  Flat, responsive, lightweight, easy customizable modal window plugin with declarative state notation and hash tracking.
 *  http://vodkabears.github.io/remodal/
 *
 *  Made by Ilya Makarov
 *  Under MIT License
 */
!(function ($) {
    'use strict';

    /**
     * Name of the plugin
     * @private
     * @type {String}
     */
    var pluginName = 'remodal';

    /**
     * Namespace for CSS and events
     * @private
     * @type {String}
     */
    var namespace = window.remodalGlobals && window.remodalGlobals.namespace || pluginName;

    /**
     * Default settings
     * @private
     * @type {Object}
     */
    var defaults = $.extend({
        hashTracking: true,
        closeOnConfirm: true,
        closeOnCancel: true,
        closeOnEscape: true,
        closeOnAnyClick: true
    }, window.remodalGlobals && window.remodalGlobals.defaults);

    /**
     * Current modal
     * @private
     * @type {Remodal}
     */
    var current;

    /**
     * Scrollbar position
     * @private
     * @type {Number}
     */
    var scrollTop;

    /**
     * Get a transition duration in ms
     * @private
     * @param {jQuery} $elem
     * @return {Number}
     */
    function getTransitionDuration($elem) {
        var duration = $elem.css('transition-duration') ||
            $elem.css('-webkit-transition-duration') ||
            $elem.css('-moz-transition-duration') ||
            $elem.css('-o-transition-duration') ||
            $elem.css('-ms-transition-duration') ||
            '0s';

        var delay = $elem.css('transition-delay') ||
            $elem.css('-webkit-transition-delay') ||
            $elem.css('-moz-transition-delay') ||
            $elem.css('-o-transition-delay') ||
            $elem.css('-ms-transition-delay') ||
            '0s';

        var max;
        var len;
        var num;
        var i;

        duration = duration.split(', ');
        delay = delay.split(', ');

        // The duration length is the same as the delay length
        for (i = 0, len = duration.length, max = Number.NEGATIVE_INFINITY; i < len; i++) {
            num = parseFloat(duration[i]) + parseFloat(delay[i]);

            if (num > max) {
                max = num;
            }
        }

        return num * 1000;
    }

    /**
     * Get a scrollbar width
     * @private
     * @return {Number}
     */
    function getScrollbarWidth() {
        if ($(document.body).height() <= $(window).height()) {
            return 0;
        }

        var outer = document.createElement('div');
        var inner = document.createElement('div');
        var widthNoScroll;
        var widthWithScroll;

        outer.style.visibility = 'hidden';
        outer.style.width = '100px';
        document.body.appendChild(outer);

        widthNoScroll = outer.offsetWidth;

        // Force scrollbars
        outer.style.overflow = 'scroll';

        // Add inner div
        inner.style.width = '100%';
        outer.appendChild(inner);

        widthWithScroll = inner.offsetWidth;

        // Remove divs
        outer.parentNode.removeChild(outer);

        return widthNoScroll - widthWithScroll;
    }

    /**
     * Lock the screen
     * @private
     */
    function lockScreen() {
        var $html = $('html');
        var lockedClass = namespace + '-is-locked';
        var paddingRight;
        var $body;

        if (!$html.hasClass(lockedClass)) {
            $body = $(document.body);

            // Zepto does not support '-=', '+=' in the `css` method
            paddingRight = parseInt($body.css('padding-right'), 10) + getScrollbarWidth();

            $body.css('padding-right', paddingRight + 'px');
            $html.addClass(lockedClass);
        }
    }

    /**
     * Unlock the screen
     * @private
     */
    function unlockScreen() {
        var $html = $('html');
        var lockedClass = namespace + '-is-locked';
        var paddingRight;
        var $body;

        if ($html.hasClass(lockedClass)) {
            $body = $(document.body);

            // Zepto does not support '-=', '+=' in the `css` method
            paddingRight = parseInt($body.css('padding-right'), 10) - getScrollbarWidth();

            $body.css('padding-right', paddingRight + 'px');
            $html.removeClass(lockedClass);
        }
    }

    /**
     * Parse a string with options
     * @private
     * @param str
     * @returns {Object}
     */
    function parseOptions(str) {
        var obj = {};
        var arr;
        var len;
        var val;
        var i;

        // Remove spaces before and after delimiters
        str = str.replace(/\s*:\s*/g, ':').replace(/\s*,\s*/g, ',');

        // Parse a string
        arr = str.split(',');
        for (i = 0, len = arr.length; i < len; i++) {
            arr[i] = arr[i].split(':');
            val = arr[i][1];

            // Convert a string value if it is like a boolean
            if (typeof val === 'string' || val instanceof String) {
                val = val === 'true' || (val === 'false' ? false : val);
            }

            // Convert a string value if it is like a number
            if (typeof val === 'string' || val instanceof String) {
                val = !isNaN(val) ? +val : val;
            }

            obj[arr[i][0]] = val;
        }

        return obj;
    }

    /**
     * Remodal constructor
     * @param {jQuery} $modal
     * @param {Object} options
     * @constructor
     */
    function Remodal($modal, options) {
        var remodal = this;
        var tdOverlay;
        var tdModal;
        var tdBg;

        remodal.settings = $.extend({}, defaults, options);

        // Build DOM
        remodal.$body = $(document.body);
        remodal.$overlay = $('.' + namespace + '-overlay');

        if (!remodal.$overlay.length) {
            remodal.$overlay = $('<div>').addClass(namespace + '-overlay');
            remodal.$body.append(remodal.$overlay);
        }

        remodal.$bg = $('.' + namespace + '-bg');
        remodal.$closeButton = $('<a href="#"></a>').addClass(namespace + '-close');
        remodal.$wrapper = $('<div>').addClass(namespace + '-wrapper');
        remodal.$modal = $modal;
        remodal.$modal.addClass(namespace);
        remodal.$modal.css('visibility', 'visible');

        remodal.$modal.append(remodal.$closeButton);
        remodal.$wrapper.append(remodal.$modal);
        remodal.$body.append(remodal.$wrapper);
        remodal.$confirmButton = remodal.$modal.find('.' + namespace + '-confirm');
        remodal.$cancelButton = remodal.$modal.find('.' + namespace + '-cancel');

        // Calculate timeouts
        tdOverlay = getTransitionDuration(remodal.$overlay);
        tdModal = getTransitionDuration(remodal.$modal);
        tdBg = getTransitionDuration(remodal.$bg);
        remodal.td = tdModal > tdOverlay ? tdModal : tdOverlay;
        remodal.td = tdBg > remodal.td ? tdBg : remodal.td;

        // Add the close button event listener
        remodal.$wrapper.on('click.' + namespace, '.' + namespace + '-close', function (e) {
            e.preventDefault();

            remodal.close();
        });

        // Add the cancel button event listener
        remodal.$wrapper.on('click.' + namespace, '.' + namespace + '-cancel', function (e) {
            e.preventDefault();

            remodal.$modal.trigger('cancel');

            if (remodal.settings.closeOnCancel) {
                remodal.close('cancellation');
            }
        });

        // Add the confirm button event listener
        remodal.$wrapper.on('click.' + namespace, '.' + namespace + '-confirm', function (e) {
            e.preventDefault();

            remodal.$modal.trigger('confirm');

            if (remodal.settings.closeOnConfirm) {
                remodal.close('confirmation');
            }
        });

        // Add the keyboard event listener
        $(document).on('keyup.' + namespace, function (e) {
            if (e.keyCode === 27 && remodal.settings.closeOnEscape) {
                remodal.close();
            }
        });

        // Add the overlay event listener
        remodal.$wrapper.on('click.' + namespace, function (e) {
            var $target = $(e.target);

            if (!$target.hasClass(namespace + '-wrapper')) {
                return;
            }

            if (remodal.settings.closeOnAnyClick) {
                remodal.close();
            }
        });

        remodal.index = $[pluginName].lookup.push(remodal) - 1;
        remodal.busy = false;
    }

    /**
     * Open a modal window
     * @public
     */
    Remodal.prototype.open = function () {

        // Check if the animation was completed
        if (this.busy) {
            return;
        }

        var remodal = this;
        var id;

        remodal.busy = true;
        remodal.$modal.trigger('open');

        id = remodal.$modal.attr('data-' + pluginName + '-id');

        if (id && remodal.settings.hashTracking) {
            scrollTop = $(window).scrollTop();
            location.hash = id;
        }

        if (current && current !== remodal) {
            current.$overlay.hide();
            current.$wrapper.hide();
            current.$body.removeClass(namespace + '-is-active');
        }

        current = remodal;

        lockScreen();
        remodal.$overlay.show();
        remodal.$wrapper.show();

        setTimeout(function () {
            remodal.$body.addClass(namespace + '-is-active');

            setTimeout(function () {
                remodal.busy = false;
                remodal.$modal.trigger('opened');
            }, remodal.td + 50);
        }, 25);
    };

    /**
     * Close a modal window
     * @public
     * @param {String|undefined} reason A reason to close
     */
    Remodal.prototype.close = function (reason) {

        // Check if the animation was completed
        if (this.busy) {
            return;
        }

        var remodal = this;

        remodal.busy = true;
        remodal.$modal.trigger({
            type: 'close',
            reason: reason
        });

        if (remodal.settings.hashTracking &&
            remodal.$modal.attr('data-' + pluginName + '-id') === location.hash.substr(1)) {

            location.hash = '';
            $(window).scrollTop(scrollTop);
        }

        remodal.$body.removeClass(namespace + '-is-active');

        setTimeout(function () {
            remodal.$overlay.hide();
            remodal.$wrapper.hide();
            unlockScreen();

            remodal.busy = false;
            remodal.$modal.trigger({
                type: 'closed',
                reason: reason
            });
        }, remodal.td + 50);
    };

    /**
     * Special plugin object for instances.
     * @public
     * @type {Object}
     */
    $[pluginName] = {
        lookup: []
    };

    /**
     * Plugin constructor
     * @param {Object} options
     * @returns {JQuery}
     * @constructor
     */
    $.fn[pluginName] = function (opts) {
        var instance;
        var $elem;

        this.each(function (index, elem) {
            $elem = $(elem);

            if ($elem.data(pluginName) == null) {
                instance = new Remodal($elem, opts);
                $elem.data(pluginName, instance.index);

                if (instance.settings.hashTracking &&
                    $elem.attr('data-' + pluginName + '-id') === location.hash.substr(1)) {

                    instance.open();
                }
            } else {
                instance = $[pluginName].lookup[$elem.data(pluginName)];
            }
        });

        return instance;
    };

    $(document).ready(function () {

        // data-remodal-target opens a modal window with the special Id.
        $(document).on('click', '[data-' + pluginName + '-target]', function (e) {
            e.preventDefault();

            var elem = e.currentTarget;
            var id = elem.getAttribute('data-' + pluginName + '-target');
            var $target = $('[data-' + pluginName + '-id=' + id + ']');

            $[pluginName].lookup[$target.data(pluginName)].open();
        });

        // Auto initialization of modal windows.
        // They should have the 'remodal' class attribute.
        // Also you can write `data-remodal-options` attribute to pass params into the modal.
        $(document).find('.' + namespace).each(function (i, container) {
            var $container = $(container);
            var options = $container.data(pluginName + '-options');

            if (!options) {
                options = {};
            } else if (typeof options === 'string' || options instanceof String) {
                options = parseOptions(options);
            }

            $container[pluginName](options);
        });
    });

    /**
     * Hashchange handler
     * @private
     * @param {Event} e
     * @param {Boolean} [closeOnEmptyHash=true]
     */
    function hashHandler(e, closeOnEmptyHash) {
        var id = location.hash.replace('#', '');
        var instance;
        var $elem;

        if (typeof closeOnEmptyHash === 'undefined') {
            closeOnEmptyHash = true;
        }

        if (!id) {
            if (closeOnEmptyHash) {

                // Check if we have currently opened modal and animation was completed
                if (current && !current.busy && current.settings.hashTracking) {
                    current.close();
                }
            }
        } else {

            // Catch syntax error if your hash is bad
            try {
                $elem = $(
                    '[data-' + pluginName + '-id=' +
                    id.replace(new RegExp('/', 'g'), '\\/') + ']'
                );
            } catch (err) {
            }

            if ($elem && $elem.length) {
                instance = $[pluginName].lookup[$elem.data(pluginName)];

                if (instance && instance.settings.hashTracking) {
                    instance.open();
                }
            }

        }
    }

    $(window).bind('hashchange.' + namespace, hashHandler);

})(window.jQuery || window.Zepto);

/*!
 * JavaScript Cookie v2.0.3
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
!function(e){if("function"==typeof define&&define.amd)define(e);else if("object"==typeof exports)module.exports=e();else{var n=window.Cookies,o=window.Cookies=e(window.jQuery);o.noConflict=function(){return window.Cookies=n,o}}}(function(){function e(){for(var e=0,n={};e<arguments.length;e++){var o=arguments[e];for(var t in o)n[t]=o[t]}return n}function n(o){function t(n,r,i){var c;if(arguments.length>1){if(i=e({path:"/"},t.defaults,i),"number"==typeof i.expires){var s=new Date;s.setMilliseconds(s.getMilliseconds()+864e5*i.expires),i.expires=s}try{c=JSON.stringify(r),/^[\{\[]/.test(c)&&(r=c)}catch(a){}return r=encodeURIComponent(String(r)),r=r.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),n=encodeURIComponent(String(n)),n=n.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent),n=n.replace(/[\(\)]/g,escape),document.cookie=[n,"=",r,i.expires&&"; expires="+i.expires.toUTCString(),i.path&&"; path="+i.path,i.domain&&"; domain="+i.domain,i.secure?"; secure":""].join("")}n||(c={});for(var p=document.cookie?document.cookie.split("; "):[],u=/(%[0-9A-Z]{2})+/g,d=0;d<p.length;d++){var f=p[d].split("="),l=f[0].replace(u,decodeURIComponent),m=f.slice(1).join("=");'"'===m.charAt(0)&&(m=m.slice(1,-1));try{if(m=o&&o(m,l)||m.replace(u,decodeURIComponent),this.json)try{m=JSON.parse(m)}catch(a){}if(n===l){c=m;break}n||(c[l]=m)}catch(a){}}return c}return t.get=t.set=t,t.getJSON=function(){return t.apply({json:!0},[].slice.call(arguments))},t.defaults={},t.remove=function(n,o){t(n,"",e(o,{expires:-1}))},t.withConverter=n,t}return n()});

// Run the cookie notification functions once the page has loaded
$( document ).ready( function() {
    cookieNotification();
    hideCookieNotification();
});
var hideCookieNotification = function() {
    
    // Hide the cookie notification after 5 sec 
    //$( '.js-cookie-notification' ).delay(5000).fadeOut( "slow" );
    
    // Set a cookie 
    //Cookies.set('CookieNotificationCookie', 'true', { expires: 365 });
};
var cookieNotification = function() {
    var setCookieNotification = function() {    
    
    // Hide the cookie notification   
      $( '.js-cookie-notification' ).fadeOut( "slow" );
        
        // Set a cookie 
      Cookies.set('CookieNotificationCookie', 'true', { expires: 365 });
      // Stop the page reloading
        return false;
};
    // Check to see if a cookie notification has been set
    if ( Cookies.get('CookieNotificationCookie') === 'true' ) {
        
        // Tell me a cookie has been set
        console.log('cookie notification set');
    } else {
        // Tell me a cookie has not been set
        console.log('cookie notification not set');
        // Show cookie notification
        $('.js-cookie-notification').css({ 'display' : 'block'});
        // Hide cookie notification if link clicked
        $('.js-cookie-notification').find('.js-cookie-notification-hide').click( setCookieNotification );
    };                  
}