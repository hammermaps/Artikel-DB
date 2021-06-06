DROP TABLE IF EXISTS `artikel`;
CREATE TABLE `artikel` (
                           `id` int(11) NOT NULL,
                           `ean` int(9) NOT NULL DEFAULT '0',
                           `name` varchar(200) NOT NULL DEFAULT 'Gira Standard 55 Schuko',
                           `tags` text
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
                          `id` int(11) NOT NULL,
                          `dbv` int(10) NOT NULL DEFAULT '0'
) DEFAULT CHARSET=utf8;

INSERT INTO `config` (`id`, `dbv`) VALUES (1, 0);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                         `id` int(11) NOT NULL,
                         `username` varchar(250) NOT NULL DEFAULT '',
                         `password` blob
) DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', 0x789c5331aa54313450292cf64dce294d77f6b60c4bf336f3cbf04bb3f40bd6cb4f0dcaf532ad8a0c2e2d2b2c76cb4a4c2b49cc34c80d2af14eca8e4a2a0c06004d361482);

ALTER TABLE `artikel` ADD PRIMARY KEY (`id`), ADD KEY `ean` (`ean`) USING BTREE;
ALTER TABLE `artikel` ADD FULLTEXT KEY `name` (`name`,`tags`);
ALTER TABLE `config` ADD PRIMARY KEY (`id`);
ALTER TABLE `users` ADD PRIMARY KEY (`id`);
ALTER TABLE `artikel` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `config` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
