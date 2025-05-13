/*
 * Copyright (c) 2013 Kevin van Zonneveld (http://kvz.io)
 * and Contributors (http://phpjs.org/authors)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

var phpjs = phpjs ? phpjs : {
    // http://phpjs.org/functions/hexdec/
    hexdec: function (hex) {
        var hex = (hex + '').replace(/[^a-f0-9]/gi, '');
        return parseInt(hex, 16);
    },

    // http://phpjs.org/functions/dechex/
    dechex: function (number) {
        if (number < 0)
            number = 0xFFFFFFFF + number + 1;
        return parseInt(number, 10).toString(16);
    },

    // http://phpjs.org/functions/chr/
    chr: function (s) {
        if (s > 0xffff) {
            s -= 0x10000;
            return String.fromCharCode(0xD800 + (s >> 10), 0xDC00 + (s & 0x3FF));
        }
        return String.fromCharCode(s);
    },

    // http://phpjs.org/functions/ord/
    ord: function (string) {
        var str = string + '',
            code = str.charCodeAt(0);
        if (0xD800 <= code && code <= 0xDBFF) {
            var hi = code;
            if (str.length === 1)
                return code;
            var low = str.charCodeAt(1);
            return ((hi - 0xD800) * 0x400) + (low - 0xDC00) + 0x10000;
        }
        if (0xDC00 <= code && code <= 0xDFFF)
            return code;
        return code;
    }
}

export default phpjs;