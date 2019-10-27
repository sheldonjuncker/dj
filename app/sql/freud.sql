-- Dream text analysis database
drop database if exists freud;
create database freud character set utf8mb4 collate utf8mb4_unicode_ci;

-- Unique, lemmatized, stemmatized words in dreams
create table freud.word(
  id bigint unsigned auto_increment primary key,
  word varchar(32) COMMENT 'Long enough for all normal real words.'
) character set utf8mb4 collate utf8mb4_unicode_ci;

-- Grouping of words by concept (e.g. dark, stars, night, sleep --> Night)
create table freud.concept(
  id bigint unsigned auto_increment primary key,
  name varchar(64) not null
) character set utf8mb4 collate utf8mb4_unicode_ci;

-- Mapping of words to concepts with certainty defalting to 1.0 (unused for now)
create table freud.word_concept(
  word_id int unsigned not null,
  concept_id int unsigned not null,
  certainty float default 1.0 -- 0.0 - 1.0
) character set utf8mb4 collate utf8mb4_unicode_ci;

-- Mapping of normalized words and their frequencies used per dream
create table freud.dream_word_freq(
  dream_id binary(16) not null,
  word_id int unsigned not null,
  frequency float not null -- 0.0 - 1.0
) character set utf8mb4 collate utf8mb4_unicode_ci;

-- Word normalizations to fix poor stemming (dies -> die, dying -> dy, should both be die)
create table freud.word_normalization(
  stemmed_word VARCHAR(32),
  word_id int unsigned not null
) character set utf8mb4 collate utf8mb4_unicode_ci;