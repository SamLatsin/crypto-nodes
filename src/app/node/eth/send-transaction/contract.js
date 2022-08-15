const Web3 = require('web3')
const ethTx = require('ethereumjs-tx').Transaction
const readline = require('readline');

function padLeadingZeros(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}

function rawTxHex(args) {
  args = JSON.parse(args);
  var provider = 'http://localhost:8545';
  var web3 = new Web3(new Web3.providers.HttpProvider(provider))
  web3.transactionConfirmationBlocks = 1;
  // Exclude 0x at the beginning of the private key
  const addressFrom = args.fromAddress;
  const privKey = Buffer.from(args.privateKey.slice(2), 'hex');
  const addressTo = args.toAddress;
  const valueInEther = args.amount;

  // const contract_address = '0x745cbccfeD4F6153d2742464051D7330cf2Bc1f7'; //testnet
  const contract_address = '0xdAC17F958D2ee523a2206206994597C13D831ec7'; //mainnet

  const method = '0xa9059cbb'; // 'transfer(address,uint256)' in keccak-256 hash
  const UINT256_addressTo = padLeadingZeros(addressTo.slice(2), 64)
  // tokens = web3.utils.numberToHex(web3.utils.toWei(valueInEther.toString(), 'ether'));
  // tokens = web3.utils.numberToHex(valueInEther * 1e18); // testnet
  tokens = web3.utils.numberToHex(valueInEther * 1e6); // mainnet
  tokens = padLeadingZeros(tokens.slice(2), 64);
  const data = method + UINT256_addressTo + tokens;
  // console.log(data);

  web3.eth.getTransactionCount(addressFrom, "pending").then((txnCount) => {
    web3.eth.getGasPrice().then((gasPrice) => {
      var txObject = {
          'nonce': web3.utils.numberToHex(txnCount),
          'to': contract_address,
          'gasPrice': web3.utils.numberToHex(gasPrice),
          'gasLimit': web3.utils.numberToHex(70000),
          'value': '0x0',
          // 'type': 2
          'data': data,
      };
      // console.log(txObject);

      const tx = new ethTx(txObject, { chain: 'mainnet'})
      // const tx = new ethTx(txObject, { chain: 'ropsten'})
      
      tx.sign(privKey)
      var serializedTx = tx.serialize();
      var rawTxHex = '0x' + serializedTx.toString('hex');
      console.log(rawTxHex);
    })
  })
  .catch(error => { console.log('Error: ', error.message); });
}
const args = process.argv.slice(2)
rawTxHex(args[0])