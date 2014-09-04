CREATE TABLE IF NOT EXISTS `archivio_contatti` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `orgid` int(11) NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_dataentry` int(11) NOT NULL,
  `pagato` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=da pagare;1=pagato',
  `nome` varchar(64) DEFAULT NULL,
  `cognome` varchar(64) DEFAULT NULL,
  `indirizzo` varchar(64) DEFAULT NULL,
  `localita` varchar(64) DEFAULT NULL,
  `cap` varchar(8) DEFAULT NULL,
  `ID_provincia` varchar(4) DEFAULT NULL,
  `sesso` varchar(1) DEFAULT NULL,
  `telefono` varchar(32) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dump dei dati per la tabella `archivio_contatti`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `areas`
--

CREATE TABLE IF NOT EXISTS `areas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `UIname` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dump dei dati per la tabella `areas`
--

INSERT INTO `areas` (`ID`, `name`, `UIname`) VALUES
(1, 'users', 'Utenti'),
(2, 'roles', 'Ruoli'),
(3, 'permessi', 'Permessi');

-- --------------------------------------------------------

--
-- Struttura della tabella `campagne`
--

CREATE TABLE IF NOT EXISTS `campagne` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `orgid` int(11) NOT NULL,
  `nome_campagna` varchar(255) NOT NULL,
  `testo` text NOT NULL,
  `data_campagna` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_invio` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dump dei dati per la tabella `campagne`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `movimenti`
--

CREATE TABLE IF NOT EXISTS `movimenti` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `orgid` int(11) NOT NULL,
  `ID_ricarica` int(11) DEFAULT '0',
  `ID_sms` int(11) DEFAULT '0',
  `data_movimento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `qnt` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dump dei dati per la tabella `movimenti`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `organizations`
--

CREATE TABLE IF NOT EXISTS `organizations` (
  `orgid` int(11) NOT NULL AUTO_INCREMENT,
  `rag_soc` varchar(255) NOT NULL,
  `ID_logo` int(11) DEFAULT NULL,
  `data_iscrizione` date NOT NULL,
  `p_iva` varchar(11) NOT NULL,
  `codfis` varchar(16) DEFAULT NULL,
  `tel` varchar(32) NOT NULL,
  PRIMARY KEY (`orgid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dump dei dati per la tabella `organizations`
--

INSERT INTO `organizations` (`orgid`, `rag_soc`, `ID_logo`, `data_iscrizione`, `p_iva`, `codfis`, `tel`) VALUES
(1, 'Campionet Managements', NULL, '2009-01-01', '01020304050', '01020304050', '000 000 000'),
(2, 'Azienda che manda SMS', NULL, '2009-10-20', '12345667895', '123456987452123', '0000202505');

-- --------------------------------------------------------

--
-- Struttura della tabella `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_area` int(11) NOT NULL,
  `sortid` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `UIname` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=permission, 1=parameter',
  `default_value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `name` (`name`),
  KEY `ID_area` (`ID_area`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dump dei dati per la tabella `permissions`
--

INSERT INTO `permissions` (`ID`, `ID_area`, `sortid`, `name`, `UIname`, `type`, `default_value`) VALUES
(1, 1, 1, 'access', 'Accesso', 0, NULL),
(2, 1, 2, 'edit', 'Modifica utenti', 0, NULL),
(3, 1, 3, 'list', 'Accedi alla lista degli utenti', 0, ''),
(4, 1, 4, 'edit_password', 'Modifica della propria password', 0, ''),
(5, 1, 5, 'password_duration', 'Giorni di durata della password', 1, '365'),
(6, 2, 1, 'access', 'Accesso ale funzioni dei ruoli', 0, ''),
(7, 2, 2, 'list', 'Visualizza la lista dei ruoli', 0, ''),
(8, 2, 3, 'edit', 'Modifica i ruoli e i permessi dei ruoli', 0, ''),
(9, 3, 1, 'access', 'Accesso agli editor di permessi', 0, ''),
(10, 3, 2, 'list', 'Visualizza la lista dei permessi', 0, ''),
(11, 3, 3, 'edit', 'Modifica, crea ed elimina permessi', 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `province`
--

CREATE TABLE IF NOT EXISTS `province` (
  `ID_provincia` varchar(4) NOT NULL,
  `provincia` varchar(80) NOT NULL,
  PRIMARY KEY (`ID_provincia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `province`
--

INSERT INTO `province` (`ID_provincia`, `provincia`) VALUES
('AG', 'Agrigento'),
('AL', 'Alessandria'),
('AN', 'Ancona'),
('AO', 'Aosta'),
('AR', 'Arezzo'),
('AP', 'Ascoli Piceno'),
('AT', 'Asti'),
('AV', 'Avellino'),
('BA', 'Bari'),
('BL', 'Belluno'),
('BN', 'Benevento'),
('BG', 'Bergamo'),
('BI', 'Biella'),
('BO', 'Bologna'),
('BZ', 'Bolzano'),
('BS', 'Brescia'),
('BR', 'Brindisi'),
('CA', 'Cagliari'),
('CL', 'Caltanissetta'),
('CB', 'Campobasso'),
('CI', 'Carbonia-Iglesias'),
('CE', 'Caserta'),
('CT', 'Catania'),
('CZ', 'Catanzaro'),
('CH', 'Chieti'),
('CO', 'Como'),
('CS', 'Cosenza'),
('CR', 'Cremona'),
('KR', 'Crotone'),
('CN', 'Cuneo'),
('EN', 'Enna'),
('FE', 'Ferrara'),
('FI', 'Firenze'),
('FG', 'Foggia'),
('FC', 'Forlì-Cesena'),
('FR', 'Frosinone'),
('GE', 'Genova'),
('GO', 'Gorizia'),
('GR', 'Grosseto'),
('IM', 'Imperia'),
('IS', 'Isernia'),
('SP', 'La Spezia'),
('AQ', 'L''Aquila'),
('LT', 'Latina'),
('LE', 'Lecce'),
('LC', 'Lecco'),
('LI', 'Livorno'),
('LO', 'Lodi'),
('LU', 'Lucca'),
('MC', 'Macerata'),
('MN', 'Mantova'),
('MS', 'Massa-Carrara'),
('MT', 'Matera'),
('ME', 'Messina'),
('MI', 'Milano'),
('MO', 'Modena'),
('NA', 'Napoli'),
('NO', 'Novara'),
('NU', 'Nuoro'),
('OT', 'Olbia-Tempio'),
('OR', 'Oristano'),
('PD', 'Padova'),
('PA', 'Palermo'),
('PR', 'Parma'),
('PV', 'Pavia'),
('PG', 'Perugia'),
('PU', 'Pesaro e Urbino'),
('PE', 'Pescara'),
('PC', 'Piacenza'),
('PI', 'Pisa'),
('PT', 'Pistoia'),
('PN', 'Pordenone'),
('PZ', 'Potenza'),
('PO', 'Prato'),
('RG', 'Ragusa'),
('RA', 'Ravenna'),
('RC', 'Reggio Calabria'),
('RE', 'Reggio Emilia'),
('RI', 'Rieti'),
('RN', 'Rimini'),
('RM', 'Roma'),
('RO', 'Rovigo'),
('SA', 'Salerno'),
('VS', 'Medio Campidano'),
('SS', 'Sassari'),
('SV', 'Savona'),
('SI', 'Siena'),
('SR', 'Siracusa'),
('SO', 'Sondrio'),
('TA', 'Taranto'),
('TE', 'Teramo'),
('TR', 'Terni'),
('TO', 'Torino'),
('OG', 'Ogliastra'),
('TP', 'Trapani'),
('TN', 'Trento'),
('TV', 'Treviso'),
('TS', 'Trieste'),
('UD', 'Udine'),
('VA', 'Varese'),
('VE', 'Venezia'),
('VB', 'Verbano-Cusio-Ossola'),
('VC', 'Vercelli'),
('VR', 'Verona'),
('VV', 'Vibo Valentia'),
('VI', 'Vicenza'),
('VT', 'Viterbo');

-- --------------------------------------------------------

--
-- Struttura della tabella `ricariche`
--

CREATE TABLE IF NOT EXISTS `ricariche` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `orgid` int(11) NOT NULL,
  `data_ricarica` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `numero_sms` int(11) NOT NULL,
  `importo` double NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dump dei dati per la tabella `ricariche`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(255) NOT NULL,
  `superuser` tinyint(4) NOT NULL DEFAULT '0',
  `developer` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dump dei dati per la tabella `roles`
--

INSERT INTO `roles` (`ID`, `role`, `superuser`, `developer`) VALUES
(1, 'Developer', 0, 1),
(2, 'Esercente', 1, 0),
(3, 'Data-entry', 0, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `roles_permissions`
--

CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_role` int(11) NOT NULL,
  `ID_permission` int(11) unsigned NOT NULL,
  `value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_role` (`ID_role`),
  KEY `ID_permission` (`ID_permission`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dump dei dati per la tabella `roles_permissions`
--

INSERT INTO `roles_permissions` (`ID`, `ID_role`, `ID_permission`, `value`) VALUES
(1, 2, 1, NULL),
(2, 2, 2, NULL),
(3, 2, 3, NULL),
(4, 2, 4, NULL),
(5, 3, 1, NULL),
(6, 3, 4, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `sms`
--

CREATE TABLE IF NOT EXISTS `sms` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `orgid` int(11) NOT NULL,
  `ID_contatto` int(11) NOT NULL,
  `ID_campagna` int(11) NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `messaggio` text NOT NULL,
  `inviato` tinyint(4) NOT NULL COMMENT '0=NO;1=SI',
  `data_invio` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `IDT` varchar(64) NOT NULL,
  `data_delivery` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dump dei dati per la tabella `sms`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_role` int(11) NOT NULL,
  `data_iscrizione` date DEFAULT NULL,
  `nome` varchar(64) NOT NULL,
  `cognome` varchar(64) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `user` varchar(64) NOT NULL,
  `password` varchar(128) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `user` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`ID`, `ID_role`, `data_iscrizione`, `nome`, `cognome`, `email`, `user`, `password`, `active`) VALUES
(1, 1, '2009-01-01', 'Admin', 'Administrator', NULL, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 1),
(2, 2, '2009-10-19', 'Esercente', 'Negoziante', NULL, 'esercente', 'e10adc3949ba59abbe56e057f20f883e', 1),
(3, 3, '2009-10-20', 'Ginetto', 'Dattilografo', NULL, 'dataentry', 'e10adc3949ba59abbe56e057f20f883e', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `users_organizations`
--

CREATE TABLE IF NOT EXISTS `users_organizations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_user` int(11) NOT NULL,
  `orgid` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dump dei dati per la tabella `users_organizations`
--

INSERT INTO `users_organizations` (`ID`, `ID_user`, `orgid`, `active`) VALUES
(1, 2, 1, 1),
(2, 3, 1, 1),
(3, 3, 2, 1);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions` FOREIGN KEY (`ID_area`) REFERENCES `areas` (`ID`) ON DELETE CASCADE;

--
-- Limiti per la tabella `roles_permissions`
--
ALTER TABLE `roles_permissions`
  ADD CONSTRAINT `roles_permissions` FOREIGN KEY (`ID_permission`) REFERENCES `permissions` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_permissions_2` FOREIGN KEY (`ID_role`) REFERENCES `roles` (`ID`) ON DELETE CASCADE;
