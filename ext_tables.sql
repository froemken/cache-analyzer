#
# Table structure for table 'tx_cacheanalyzer_expression'
#
CREATE TABLE tx_cacheanalyzer_expression
(
	title                   varchar(100) DEFAULT ''  NOT NULL,
	is_regexp               tinyint(1)   DEFAULT '0' NOT NULL,
	throw_exception         tinyint(1)   DEFAULT '0' NOT NULL,
	throw_exception_fe_only tinyint(1)   DEFAULT '1' NOT NULL,
	expression              varchar(255) DEFAULT ''  NOT NULL,
	cache_configurations    text
);
