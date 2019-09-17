-- phpMyAdmin SQL Dump
-- Время создания: Авг 20 2019 г., 15:26
-- Версия PHP: 5.6.32
--
-- База данных: `raio`
--
-- --------------------------------------------------------
-- Структура таблицы `raio_oktmo`
--

CREATE TABLE raio_oktmo (
  oktmo   varchar(32)   PRIMARY KEY COMMENT 'код ОКТМО региона',
  okato   varchar(32)   COMMENT 'код ОКАТО',
  zapros  varchar(255)  COMMENT 'строка запроса к nominatim.openstreetmap.org',
  geojson longtext      COMMENT 'строка JSON полигона',
  kratko  varchar(128)  COMMENT 'краткое название региона',
  level   int           COMMENT 'уровень региона 100-высший',
  wdat    timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'время записи'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='гео данные регионов ОКТМО';

