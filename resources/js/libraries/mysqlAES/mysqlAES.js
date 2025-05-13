/*
 *                       Written by JoungKyun.Kim
 *            Copyright (c) 2013 JoungKyun.Kim <http://oops.org>
 *
 * -----------------------------------------------------------------------------
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the GNU General Public License as published by the Free
 *  Software Foundation; either version 2.1 of the License, or (at your option)
 *  any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * -----------------------------------------------------------------------------
 * This program is based on js-mcrypt 1.1 <https://code.google.com/p/js-mcrypt/>
 *
 * $Id: mysqlAES.js 6 2013-09-28 17:52:12Z oops $
 */
//import phpjs from "../libraries/phpjs.js";
import phpjs from "./phpjs.js";
import Rijndael from './rijndael.js';


//this creates a static class mysqlAES that is already initialized
var mysqlAES = mysqlAES ? mysqlAES : new function () {
    /*
     * Private methods
     */
    var RijndaelCall = function (cipher, block, key, encrypt) {
        // key padding
        //if ( key.length < 32 )
        //  key += Array(33 - key.length).join (String.fromCharCode (0));

        if (key.length < 32 && key.length != 16 && key.length != 24) {
            let keyplus;
            if (key.length < 16)
                keyplus = 17 - key.length;
            else if (key.length < 24)
                keyplus = 24 - key.length;
            else
                keyplus = 33 - key.length;
            key += Array(keyplus).join(String.fromCharCode(0));
        }

        if (encrypt)
            Rijndael.Encrypt(block, key);
        else
            Rijndael.Decrypt(block, key);
        return block;
    };

    /*
     * Crypt
     * This function can encrypt or decrypt text
     */
    var Crypt = function (encrypt, text, key) {
        if (!key)
            key = '01234567890123456789012345678901';
        if (!text)
            return true;
        var blockS = 16;
        var chunkS = blockS;
        var data = new Array(blockS);

        // On Encrypt mode, convert multibyte character to single byte character
        if (encrypt)
            text = bytes(text);

        // padding
        var chunks = Math.floor(text.length / blockS);
        var blocks = blockS * (chunks + 1);
        var padlen = blocks - text.length;
        var orig = text.length;

        text += Array(padlen + 1).join(String.fromCharCode(padlen));
        var out = '';

        for (var i = 0; i <= chunks; i++) {
            for (var j = 0; j < chunkS; j++)
                data[j] = text.charCodeAt((i * chunkS) + j);

            RijndaelCall('rijndael-128', data, key, encrypt);

            for (var j = 0; j < chunkS; j++)
                out += String.fromCharCode(data[j]);
        }

        if (!encrypt) {
           

            var last = out.charCodeAt(out.length - 17).toString(10);
            last = out.length - 16 - last;

            var buf = '';
            for (i = 0; i < last; i++)
                buf += out[i];
            buf += '';

            return unbytes(buf);
        }

        return out;
    };

    /*
     * Privates function
     */
    var bytes = function (str) {
        var ret = '';
        for (let i = 0; i < str.length; i++) {
            var charCode = str.charCodeAt(i);
            if ((charCode >> 11) || (charCode >> 7)) {
                var tmp = encodeURI(str[i]);
                var buf = tmp.split('%');
                for (x = 1; x < buf.length; x++)
                    ret += phpjs.chr(parseInt(buf[x], 16));
            } else
                ret += str[i];
        }

        return ret;
    }

    var unbytes = function (str) {
        let ret = '';
        for (let i = 0; i < str.length; i++) {
            var charCode = str.charCodeAt(i);
            if (charCode > 128) {
                try {
                    c = '%' + phpjs.dechex(phpjs.ord(str[i])) +
                        '%' + phpjs.dechex(phpjs.ord(str[i + 1])) +
                        '%' + phpjs.dechex(phpjs.ord(str[i + 2]));
                    ret += decodeURI(c);
                    i += 2;
                } catch (e) {
                    try {
                        c = '%' + phpjs.dechex(phpjs.ord(str[i])) +
                            '%' + phpjs.dechex(phpjs.ord(str[i + 1]));
                        ret += decodeURI(c);
                        i++;
                    } catch (e) {
                        ret += str[i];
                    }
                }
            } else {
                ret += str[i];
            }
        }

        return ret;
    }

    /*
     * Public methods
     */
    var pub = {};

    /* mysql hex */
    pub.hex = function (s) {
        var i, l, o = '', n;
        s += '';
        for (let i = 0, l = s.length; i < l; i++) {
            n = s.charCodeAt(i).toString(16).toUpperCase();
            o += n.length < 2 ? "0" + n : n;
        }
        return o;
    }

    /* mysql unhex */
    pub.unhex = function (s) {
        if ((s.length % 2) != 0)
            return '"' + s + '" is wrong hex strings!';

        var out = '';
        for (let i = 0; i < s.length; i += 2) {
            var buf = phpjs.hexdec(s[i] + s[i + 1]);
            out += phpjs.chr(buf);
        }

        return out;
    }

    /* Encrypt */
    pub.Encrypt = function (message, key) {
        return Crypt(true, message, key);
    };

    /* Decrypt */
    pub.Decrypt = function (ctext, key) {
        return Crypt(false, ctext, key);
    };

    return pub;
};

export default mysqlAES;
