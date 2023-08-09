# WT SEO Meta templates
Plugin for using variables in the `<title>` and meta-description tags. Allows you to use templates for the `<title>` tag and the meta-description tag. Accepts data (including SEO templates) from additional plugin providers.

## How it works?
To work, you install minimum 2 plugins:
- Main WT SEO Meta templates
- Plugin provider for your component (for example, for Virtuemart)
The plugin provider creates and passes variables and SEO templates to the main plugin. All settings are made in the provider plugin.
## Example
### Seo template for &lt;title&gt;
For example, you could create a `<title>` seo template like `{CC_ARTICLE_TITLE}. {CC_ARTICLE_FIELD_14_TITLE} {CC_ARTICLE_FIELD_14_VALUE}`. 
### Seo template for meta description
You could create a meta description seo template like `{CC_ARTICLE_INTRO}`, where `{CC_ARTICLE_INTRO}` is article intro text trimmed to the specified number of characters.
## Available plugins-providers
At the moment, the following plugins providers are created:
- WT SEO Meta templates - Virtuemart (online store)
- WT SEO Meta templates - JoomShopping (online store)
- WT SEO Meta templates - MyCitySelector (Joomla multi-region component). [My City Selector component on GitHub](https://github.com/joomx/mycityselector)
- WT SEO Meta templates - Content. Joomla content articles and categories.
- WT SEO Meta templates - Tags. Joomla list of tags and items by tag(s).
- WT SEO Meta templates - Phoca Gallery, image gallery Phoca Gallery.
## System info
Since v.2.0.0 plugin works only with Joomla 4 because it has made with Joomla 4 plugin structure.