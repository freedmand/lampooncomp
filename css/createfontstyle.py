import os

TEMPLATE = """@font-face {
	font-family: '%s';
	src: url('%s/%s') format('woff');
}\n"""
DIR = 'fonts'

extension = '.woff'

fonts = filter(lambda x: x.endswith(extension), os.listdir(DIR))
font_names = map(lambda x: x[:-len(extension)], fonts)

stylesheet = open('fonts.css','w')
for i in xrange(len(fonts)):
	stylesheet.write(TEMPLATE % (font_names[i], DIR, fonts[i]))
stylesheet.close()