#
# Table structure for table 'tt_address'
#
CREATE TABLE tt_address (
  clientip tinytext NOT NULL,
	time_subscription int(11) unsigned DEFAULT '0' NOT NULL,
	time_sendsubscriptionmail int(11) unsigned DEFAULT '0' NOT NULL,
	time_approvesubscription int(11) unsigned DEFAULT '0' NOT NULL,
);
