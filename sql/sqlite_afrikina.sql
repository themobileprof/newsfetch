
/**
 * Generated by Database Modeler Lite
 * https://play.google.com/store/apps/details?id=adrian.adbm
 * 
 * Created: Jan 7, 2020
*/


/*
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `news_sources`;
*/


CREATE TABLE `news_sources` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `rss` TEXT UNIQUE,
    `catId` INTEGER DEFAULT 1,
    `fail` INTEGER DEFAULT 0,
    `activ` TEXT DEFAULT 1
);


CREATE TABLE `articles` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `guId` TEXT UNIQUE,
    `sourceId` INTEGER,
    `title` TEXT,
    `description` TEXT,
    `img` TEXT,
    `url` TEXT,
    `articleDate` TEXT,
    `catId` INTEGER DEFAULT 1,
    FOREIGN KEY (`sourceId`) REFERENCES `news_sources`(`id`)
);

