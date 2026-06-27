SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Recipe: the cocktail itself (aggregate root)
-- --------------------------------------------------------
CREATE TABLE `cocktails_recipe` (
  `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `title`           VARCHAR(255)     NOT NULL DEFAULT '',
  `slug`            VARCHAR(255)     NOT NULL DEFAULT '',
  `cid`             INT UNSIGNED     NOT NULL DEFAULT 0,
  `glass_id`        INT UNSIGNED     NOT NULL DEFAULT 0,
  `difficulty`      TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `prep_time`       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `servings`        TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `is_alcoholic`    TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `summary`         VARCHAR(500)     NOT NULL DEFAULT '',
  `method`          TEXT             NULL,
  `garnish`         VARCHAR(255)     NOT NULL DEFAULT '',
  `image`           VARCHAR(255)     NOT NULL DEFAULT '',
  `rating_sum`      INT UNSIGNED     NOT NULL DEFAULT 0,
  `rating_count`    INT UNSIGNED     NOT NULL DEFAULT 0,
  `rating_avg`      DECIMAL(3,2)     NOT NULL DEFAULT 0.00,
  `favorites_count` INT UNSIGNED     NOT NULL DEFAULT 0,
  `views`           INT UNSIGNED     NOT NULL DEFAULT 0,
  `featured`        TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `uid`             INT UNSIGNED     NOT NULL DEFAULT 0,
  `online`          TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `created`         INT UNSIGNED     NOT NULL DEFAULT 0,
  `updated`         INT UNSIGNED     NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`),
  KEY `idx_category` (`cid`),
  KEY `idx_glass` (`glass_id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_online_rating` (`online`, `rating_avg`),
  KEY `idx_online_created` (`online`, `created`),
  KEY `idx_featured` (`featured`, `online`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Ingredient: admin-curated master list (a controlled vocabulary)
-- --------------------------------------------------------
CREATE TABLE `cocktails_ingredient` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)     NOT NULL DEFAULT '',
  `slug`        VARCHAR(100)     NOT NULL DEFAULT '',
  `type`        TINYINT UNSIGNED NOT NULL DEFAULT 6,
  `abv`         DECIMAL(5,2)     NOT NULL DEFAULT 0.00,
  `description` TEXT             NULL,
  `image`       VARCHAR(255)     NOT NULL DEFAULT '',
  `weight`      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `online`      TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`),
  KEY `idx_type` (`type`),
  KEY `idx_online` (`online`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- RecipeIngredient: a measured ingredient line on a recipe
-- (the book's "MeasuredIngredient" value object, persisted)
-- --------------------------------------------------------
CREATE TABLE `cocktails_recipe_ingredient` (
  `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `recipe_id`     INT UNSIGNED     NOT NULL DEFAULT 0,
  `ingredient_id` INT UNSIGNED     NOT NULL DEFAULT 0,
  `amount`        DECIMAL(8,2)     NOT NULL DEFAULT 0.00,
  `unit`          VARCHAR(16)      NOT NULL DEFAULT '',
  `note`          VARCHAR(255)     NOT NULL DEFAULT '',
  `is_optional`   TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `weight`        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_recipe` (`recipe_id`, `weight`),
  KEY `idx_ingredient` (`ingredient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Rating: one star rating (1-5) per user per recipe
-- --------------------------------------------------------
CREATE TABLE `cocktails_rating` (
  `id`        INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `recipe_id` INT UNSIGNED     NOT NULL DEFAULT 0,
  `uid`       INT UNSIGNED     NOT NULL DEFAULT 0,
  `stars`     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created`   INT UNSIGNED     NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_recipe_user` (`recipe_id`, `uid`),
  KEY `idx_recipe` (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Favorite: user bookmark of a recipe
-- --------------------------------------------------------
CREATE TABLE `cocktails_favorite` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `uid`       INT UNSIGNED NOT NULL DEFAULT 0,
  `created`   INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_recipe_user` (`recipe_id`, `uid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Category: cocktail families (Classics, Tiki, Mocktails, ...)
-- --------------------------------------------------------
CREATE TABLE `cocktails_category` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `pid`         INT UNSIGNED     NOT NULL DEFAULT 0,
  `title`       VARCHAR(255)     NOT NULL DEFAULT '',
  `slug`        VARCHAR(255)     NOT NULL DEFAULT '',
  `description` TEXT             NULL,
  `image`       VARCHAR(255)     NOT NULL DEFAULT '',
  `color`       VARCHAR(10)      NOT NULL DEFAULT '',
  `weight`      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `online`      TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`),
  KEY `idx_parent` (`pid`),
  KEY `idx_online_weight` (`online`, `weight`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Glass: serving glassware (Coupe, Highball, Rocks, ...)
-- --------------------------------------------------------
CREATE TABLE `cocktails_glass` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)     NOT NULL DEFAULT '',
  `slug`        VARCHAR(100)     NOT NULL DEFAULT '',
  `description` VARCHAR(255)     NOT NULL DEFAULT '',
  `image`       VARCHAR(255)     NOT NULL DEFAULT '',
  `weight`      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tag + recipe_tag pivot (folksonomy / discovery)
-- --------------------------------------------------------
CREATE TABLE `cocktails_tag` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`  VARCHAR(50)  NOT NULL DEFAULT '',
  `slug`  VARCHAR(50)  NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cocktails_recipe_tag` (
  `recipe_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `tag_id`    INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`recipe_id`, `tag_id`),
  KEY `idx_tag` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
