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
  const addressTo = args.toAddress;
  const valueInEther = args.amount;

  const method = '0xa9059cbb'; // 'transfer(address,uint256)' in keccak-256 hash
  const UINT256_addressTo = padLeadingZeros(addressTo.slice(2), 64)
  // tokens = web3.utils.numberToHex(web3.utils.toWei(valueInEther.toString(), 'ether')); 
  // tokens = web3.utils.numberToHex(valueInEther * 1e18); // testnet
  tokens = web3.utils.numberToHex(valueInEther * 1e6); // mainnet
  tokens = padLeadingZeros(tokens.slice(2), 64);
  const data = method + UINT256_addressTo + tokens;

  const bytes_count = data.slice(2).length / 2;
  web3.eth.getGasPrice().then((gasPrice) => {
    const gasUsed = (200 * bytes_count + 21000);
    const fee = gasUsed * gasPrice / 1000000000000000000;
    console.log(fee);
  })
}
const args = process.argv.slice(2)
rawTxHex(args[0])