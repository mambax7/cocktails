# Cocktails — a XOOPS recipe module

Submit, rate and discover cocktail recipes. Inspired by the "cocktail rater" application from
*Modern Application Development with PHP* (the book's domain model: recipes, measured ingredients,
star ratings, an admin-curated ingredient list and units), then taken much further for a real,
production XOOPS 2.7 module.

## Highlights

- **Domain-driven core.** Immutable value objects (`Domain\Unit`, `Domain\Difficulty`,
  `Domain\IngredientType`) model the parts the book stringly-typed. `Unit` knows its label and, where
  meaningful, its conversion factor to millilitres, so quantities are first-class — not free text.
- **Rich measured ingredients.** A dynamic add/remove editor builds recipe lines of
  *amount + unit + ingredient (+ optional note)* against a reusable, admin-managed ingredient list.
- **Ratings & favorites.** 1–5 star ratings (one per user, cached average/count for fast sorting),
  AJAX rating widget, and per-user favorites/bookmarks.
- **Discovery.** Categories, glassware, tags, full-text search, and "browse by ingredient", plus
  filterable/sortable browsing (top rated, newest, most viewed, A–Z).
- **Media & polish.** Image upload, a responsive Bootstrap-friendly card UI, a print-friendly recipe
  view, and three blocks (Top Rated, Newest, Random).

## Architecture

```
class/
  Domain/        value objects (Unit, Difficulty, IngredientType)
  *.php          XoopsObject entities (Recipe, Ingredient, RecipeIngredient, Rating,
                 Favorite, Category, Glass, Tag) + their *Handler persistence classes
  Form/          XoopsThemeForm builders
  Helper.php     module helper (handler factory)
  Utility.php    extends mtools SysUtility (slugify, uniqueSlug, renderStars, ...)
```

The module **reuses `mtools`** throughout: `Common\SysUtility`, `Common\Configurator`,
`Common\Blocksadmin`, `Common\Migrate`, `Common\DirectoryChecker`, `Common\TestdataButtons`,
`Module\Installer`, `Module\ModuleContext` and `Module\ConsumerRuntime`.

## Requirements

- XOOPS >= 2.7.0, PHP >= 8.2, MySQL >= 5.7
- `mtools` >= 1.2.0 (installed first)

## Install

1. Copy the `cocktails/` folder to `XOOPS/modules/`.
2. Admin → Modules → install **Cocktails**.
3. Set **Permissions** (who may submit, auto-approve, edit own, rate) and **Preferences**.

## Data model (tables, prefix `cocktails_`)

`recipe`, `ingredient`, `recipe_ingredient`, `rating`, `favorite`, `category`, `glass`, `tag`,
`recipe_tag`.

## License

GNU GPL 2.0 or later.
