# Project information
project = 'Faylin'
copyright = '2021, Danae Dekker'
author = 'Danae'

# Extensions
extensions = ['sphinxext.opengraph']

# Templates
templates_path = ['_templates']
exclude_patterns = ['_build', 'Thumbs.db', '.DS_Store']


# HTML output options
html_theme = 'furo'
html_theme_options = {
  "light_css_variables": {
    "color-brand-primary": "#5EB2CE",
    "color-brand-content": "#5EB2CE",
  },
  "dark_css_variables": {
    "color-brand-primary": "#5EB2CE",
    "color-brand-content": "#5EB2CE",
  },
}
html_static_path = ['_static']
html_logo = '_static/icon-64px.png'

ogp_site_url = 'https://docs.fayl.in/'
