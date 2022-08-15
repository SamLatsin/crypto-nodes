-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 12, 2022 at 11:49 AM
-- Server version: 8.0.27-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `main`
--

-- --------------------------------------------------------

--
-- Table structure for table `eth`
--

CREATE TABLE `eth` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Table structure for table `functions`
--

CREATE TABLE `functions` (
  `id` int NOT NULL,
  `ticker` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `create` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `newAddress` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `getAddress` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `send` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `history` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `balance` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `status` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `fee` text,
  `recover` text,
  `recoverStatus` text,
  `getTransaction` text,
  `isRecovering` text,
  `startCronRecover` text,
  `parsePrivKeys` text,
  `checkParsedBalances` text,
  `getFileRecoveredStat` text,
  `removeWallet` text,
  `checkProcess` text,
  `getConfirmations` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `functions`
--

INSERT INTO `functions` (`id`, `ticker`, `create`, `newAddress`, `getAddress`, `send`, `history`, `balance`, `status`, `fee`, `recover`, `recoverStatus`, `getTransaction`, `isRecovering`, `startCronRecover`, `parsePrivKeys`, `checkParsedBalances`, `getFileRecoveredStat`, `removeWallet`, `checkProcess`, `getConfirmations`) VALUES
(1, 'btc', 'createWalletBTC', 'createNewAddressBTC', NULL, 'sendBTC', 'getHistoryBTC', 'getBalanceBTC', 'getStatusBTC', 'getFeeBTC', 'recoverWalletBTC', 'getRecoverStatusBTC', 'getTransactionBTC', 'isRecoveringBTC', 'startCronRecoverBTC', 'parsePrivKeysBTC', 'checkParsedBalancesBTC', 'getFileRecoveredStatBTC', 'removeWalletBTC', 'checkProcessBTC', 'getConfirmationsBTC'),
(2, 'dash', 'createWalletDASH', 'createNewAddressDASH', NULL, 'sendDASH', 'getHistoryDASH', 'getBalanceDASH', 'getStatusDASH', 'getFeeDASH', 'recoverWalletDASH', 'getRecoverStatusDASH', 'getTransactionDASH', 'isRecoveringDASH', 'startCronRecoverDASH', NULL, NULL, NULL, NULL, 'checkProcessDASH', NULL),
(3, 'erc20', 'createWalletERC20', 'createNewAddressERC20', NULL, 'sendERC20', 'getHistoryERC20', 'getBalanceERC20', 'getStatusERC20', 'getFeeERC20', 'recoverWalletERC20', 'getRecoverStatusERC20', 'getTransactionERC20', 'isRecoveringERC20', 'startCronRecoverERC20', NULL, NULL, NULL, NULL, 'checkProcessERC20', NULL),
(4, 'eth', 'createWalletETH', 'createNewAddressETH', 'getAddressETH', 'sendETH', 'getHistoryETH', 'getBalanceETH', 'getStatusETH', 'getFeeETH', 'recoverWalletETH', 'getRecoverStatusETH', 'getTransactionETH', 'isRecoveringETH', 'startCronRecoverETH', NULL, NULL, NULL, NULL, 'checkProcessETH', NULL),
(5, 'trc20', 'createWalletTRC20', 'createNewAddressTRC20', NULL, 'sendTRC20', 'getHistoryTRC20', 'getBalanceTRC20', 'getStatusTRC20', 'getFeeTRC20', 'recoverWalletTRC20', 'getRecoverStatusTRC20', 'getTransactionTRC20', 'isRecoveringTRC20', 'startCronRecoverTRC20', NULL, NULL, NULL, NULL, 'checkProcessTRC20', NULL),
(6, 'xmr', 'createWalletXMR', 'createNewAddressXMR', NULL, 'sendXMR', 'getHistoryXMR', 'getBalanceXMR', 'getStatusXMR', 'getFeeXMR', 'recoverWalletXMR', 'getRecoverStatusXMR', 'getTransactionXMR', 'isRecoveringXMR', 'startCronRecoverXMR', NULL, NULL, NULL, NULL, 'checkProcessXMR', NULL),
(7, 'zec', 'createWalletZEC', 'createNewAddressZEC', 'getAddressZEC', 'sendZEC', 'getHistoryZEC', 'getBalanceZEC', 'getStatusZEC', 'getFeeZEC', 'recoverWalletZEC', 'getRecoverStatusZEC', 'getTransactionZEC', 'isRecoveringZEC', 'startCronRecoverZEC', NULL, NULL, NULL, NULL, 'checkProcessZEC', NULL),
(8, 'trx', 'createWalletTRX', 'createNewAddressTRX', 'getAddressTRX', 'sendTRX', 'getHistoryTRX', 'getBalanceTRX', 'getStatusTRX', 'getFeeTRX', 'recoverWalletTRX', 'getRecoverStatusTRX', 'getTransactionTRX', 'isRecoveringTRX', 'startCronRecoverTRX', NULL, NULL, NULL, NULL, 'checkProcessTRX', NULL),
(9, 'zect', 'createWalletZECT', 'createNewAddressZECT', 'getAddressZECT', 'sendZECT', 'getHistoryZECT', 'getBalanceZECT', 'getStatusZECT', 'getFeeZECT', 'recoverWalletZECT', 'getRecoverStatusZECT', 'getTransactionZECT', 'isRecoveringZECT', 'startCronRecoverZECT', NULL, NULL, NULL, NULL, 'checkProcessZECT', NULL),
(10, 'test', 'createWalletTEST', 'createNewAddressTEST', 'getAddressTEST', 'sendTEST', 'getHistoryTEST', 'getBalanceTEST', 'getStatusTEST', 'getFeeTEST', 'recoverWalletTEST', 'getRecoverStatusTEST', 'getTransactionTEST', 'isRecoveringTEST', 'startCronRecoverTEST', NULL, NULL, NULL, NULL, 'checkProcessTEST', NULL);

--
-- Table structure for table `recover_queue`
--

CREATE TABLE `recover_queue` (
  `id` int NOT NULL,
  `ticker` text,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `walletName` text,
  `recovering` int NOT NULL DEFAULT '0',
  `startHeight` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Table structure for table `trx`
--

CREATE TABLE `trx` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int NOT NULL,
  `ticker` text NOT NULL,
  `name` text NOT NULL,
  `privateKey` text NOT NULL,
  `mnemonic` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `walletToken` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `lastSync` datetime DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Table structure for table `zcash`
--

CREATE TABLE `zcash` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Table structure for table `zcash_transparent`
--

CREATE TABLE `zcash_transparent` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for table `eth`
--
ALTER TABLE `eth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `functions`
--
ALTER TABLE `functions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recover_queue`
--
ALTER TABLE `recover_queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trx`
--
ALTER TABLE `trx`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zcash`
--
ALTER TABLE `zcash`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zcash_transparent`
--
ALTER TABLE `zcash_transparent`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eth`
--
ALTER TABLE `eth`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `functions`
--
ALTER TABLE `functions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `recover_queue`
--
ALTER TABLE `recover_queue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `trx`
--
ALTER TABLE `trx`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=485;

--
-- AUTO_INCREMENT for table `zcash`
--
ALTER TABLE `zcash`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `zcash_transparent`
--
ALTER TABLE `zcash_transparent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
