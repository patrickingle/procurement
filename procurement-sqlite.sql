-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE IF NOT EXISTS `bids` (
	`seq`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`solnum`	TEXT NOT NULL DEFAULT 0,
	`email`	TEXT NOT NULL,
	`amount`	REAL NOT NULL DEFAULT 0,
	`comments`	TEXT NOT NULL,
	`bidtime`	NUMERIC NOT NULL DEFAULT 0,
	`filename`	TEXT NOT NULL
);
-- --------------------------------------------------------

--
-- Table structure for table `echeck`
--

CREATE TABLE IF NOT EXISTS `echeck` (
  `seq` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `email` TEXT NOT NULL,
  `rtnaba` TEXT NOT NULL,
  `acctno` TEXT NOT NULL,
  `bankinfo` TEXT NOT NULL,
  `holderinfo` TEXT NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `seq` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `solnum` TEXT NOT NULL,
  `question` TEXT NOT NULL,
  `answer` TEXT NOT NULL,
  `email` TEXT NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `solicitations`
--

CREATE TABLE IF NOT EXISTS `solicitations` (
  `seq` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `number` TEXT NOT NULL,
  `title` TEXT NOT NULL,
  `dueTEXT` TEXT NOT NULL DEFAULT '0000-00-00',
  `budget` NUMERIC NOT NULL DEFAULT '0.00',
  `synopsis` TEXT NOT NULL,
  `description` TEXT NOT NULL,
  `filename` TEXT NOT NULL,
  `onlinebid` BOOL NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE IF NOT EXISTS `vendor` (
  `seq` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `appdata`	INTEGER NOT NULL DEFAULT 0,
  `signature` TEXT NOT NULL,
  `visa_accepted` BOOL NOT NULL DEFAULT FALSE,
  `name` TEXT NOT NULL,
  `address` TEXT NOT NULL,
  `city` TEXT NOT NULL,
  `state` TEXT NOT NULL,
  `zipcode` TEXT NOT NULL,
  `contact` TEXT NOT NULL,
  `title` TEXT NOT NULL,
  `phone` TEXT NOT NULL,
  `fax` TEXT NOT NULL,
  `fein` TEXT NOT NULL,
  `email` TEXT NOT NULL,
  `website` TEXT NOT NULL,
  `payment_mail` BOOL NOT NULL DEFAULT FALSE,
  `mailing_address` TEXT NOT NULL,
  `mailing_city` TEXT NOT NULL,
  `mailing_state` TEXT NOT NULL,
  `mailing_zipcode` TEXT NOT NULL,
  `payment_terms` TEXT NOT NULL,
  `shipping_terms` TEXT NOT NULL,
  `business_type` TEXT NOT NULL,
  `business_structure` TEXT NOT NULL,
  `specialty` TEXT NOT NULL,
  `small_business` BOOL NOT NULL DEFAULT FALSE,
  `minority_business` BOOL NOT NULL DEFAULT FALSE,
  `minority_type` TEXT NOT NULL,
  `certified_business` BOOL NOT NULL DEFAULT FALSE,
  `certification_authority` TEXT NOT NULL,
  `fee` NUMERIC NOT NULL DEFAULT '0.00',
  `solemail` BOOL NOT NULL DEFAULT FALSE,
  `validated` BOOL NOT NULL DEFAULT FALSE
);