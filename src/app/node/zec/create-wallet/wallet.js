function gen(mnemonic = null, private_key = null) {
    if (!mnemonic) {
        mnemonic = require("bip39").generateMnemonic();
    }
    const HDWallet = require('ethereum-hdwallet');
    const hdwallet = HDWallet.fromMnemonic(mnemonic);
    if (!private_key) {
    	private_key = hdwallet.derive(`m/44'/133'/0'/0`).getPrivateKey().toString('hex');
    	address = hdwallet.derive(`mm/44'/133'/0'/0`).getAddress().toString('hex');
    	private_key = `0x${private_key}`;
    	address = `0x${address}`;
    }
    else {
    	// console.log(private_key.slice(2));
    	// const privateKeyToAddress = require('ethereum-private-key-to-address');
    	// address = privateKeyToAddress(Buffer.from(private_key.slice(2), 'hex')).toLowerCase();
    }
    const res = {
      "mnemonic": mnemonic,
      "privateKey": private_key,
      "address": address
    };
    console.log(JSON.stringify(res));
}
const args = process.argv.slice(2)
gen(args[0], args[1]);

