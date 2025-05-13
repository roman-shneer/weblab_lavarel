import mysqlAES from './mysqlAES/mysqlAES';
const Enc = {
   
    encrypt: (plaintext: string, password: string) => {        
        var enc = mysqlAES.hex(mysqlAES.Encrypt(plaintext, password));
        return enc;        
    },

    decrypt: (ciphertext: string, password: string) => {
        var plaintext = mysqlAES.Decrypt(mysqlAES.unhex(ciphertext), password);        
        return plaintext;
    }
};

export default Enc;