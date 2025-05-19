



### Block style variations
- Register them via php in `inc/block-styles.php`
- Per block style, add a folder in `src/scss/blocks`
- Per block, add file with the block name and prefix, e.g. `core-cover.scss`
- Exampe: block style variation for the pagraph block: `is-style-indicator` in `src/scss/blocks/indicator/core-paragraph.scss` 


### Template parts
- upa25 comes multiple template parts, like footer-wide, fooder-centered et cetera
- to register new template parts:
  - create a new html in the `parts` folder and link to a pattern
  - create a pattern in the `pattern` folder
  - register the part in theme.json

### Patterns
- Register category in `inc/block-patterns.php`


### Block  variations
- Register block variations via php in `inc/block-variations.php`
