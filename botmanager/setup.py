from distutils.core import setup
import py2exe
import os
import re
import gtk

GTK_RUNTIME_DIR = os.path.join(os.path.split(os.path.dirname(gtk.__file__))[0], "runtime")
assert os.path.exists(GTK_RUNTIME_DIR), "Cannot find GTK runtime data"
GTK_RUNTIME_BIN_DIR = os.path.join(GTK_RUNTIME_DIR, 'bin')
GTK_THEME_ENGINES_DIR = os.path.join("lib", "gtk-2.0", "2.10.0", "engines")

def generate_data_files(prefix, tree, file_filter=None):
	"""
	Walk the filesystem starting at "prefix" + "tree", producing a list of files
	suitable for the data_files option to setup(). The prefix will be omitted
	from the path given to setup(). For example, if you have

		C:\Python26\Lib\site-packages\gtk-2.0\runtime\etc\...

	...and you want your "dist\" dir to contain "etc\..." as a subdirectory,
	invoke the function as

		generate_data_files(
			r"C:\Python26\Lib\site-packages\gtk-2.0\runtime",
			r"etc")

	If, instead, you want it to contain "runtime\etc\..." use:

		generate_data_files(
			r"C:\Python26\Lib\site-packages\gtk-2.0",
			r"runtime\etc")

	Empty directories are omitted.

	file_filter(root, fl) is an optional function called with a containing
	directory and filename of each file. If it returns False, the file is
	omitted from the results.
	"""
	data_files = []
	for root, dirs, files in os.walk(os.path.join(prefix, tree)):
		if 'build' in dirs:
			dirs.remove('build')
		if 'dist' in dirs:
			dirs.remove('dist')

		to_dir = os.path.relpath(root, prefix)

		if file_filter is not None:
			file_iter = (fl for fl in files if file_filter(root, fl))
		else:
			file_iter = files

		data_files.append((to_dir, [os.path.join(root, fl) for fl in file_iter]))

	non_empties = [(to, fro) for (to, fro) in data_files if fro]

	return non_empties

class Target:
	def __init__(self, **kw):
		self.__dict__.update(kw)
		# for the versioninfo resources
		self.version = '1.0'
		self.company_name = 'budabot.com'
		self.copyright = 'GPL'
		self.name = 'Budabot Bot Manager'
		self.description = 'Bot Manager application for Budabot.'

windows_target = Target(
	script = 'botmanager.py',
	dest_base = 'BotManager',
	icon_resources = [(0, os.path.join('icon', 'icon.ico'))]
)

# include these in the distribution, but do not bundle them
gtkDlls = [
	"intl.dll",
	"libatk-1.0-0.dll",
	"libgdk_pixbuf-2.0-0.dll",
	"libgdk-win32-2.0-0.dll",
	"libglib-2.0-0.dll",
	"libgmodule-2.0-0.dll",
	"libgobject-2.0-0.dll",
	"libgthread-2.0-0.dll",
	"libgtk-win32-2.0-0.dll",
	"libpango-1.0-0.dll",
	'libpangocairo-1.0-0.dll',
	"libpangowin32-1.0-0.dll",
	'libgio-2.0-0.dll',
	'freetype6.dll',
	'libcairo-2.dll',
	'libexpat-1.dll',
	'libfontconfig-1.dll',
	'libpangoft2-1.0-0.dll',
	'libpng14-14.dll'
]

# do not include any of these files
dllExcludes = [
	'w9xpopen.exe'
	'API-MS-Win-Core-Debug-L1-1-0.dll',
	'API-MS-Win-Core-Debug-L1-1-0.dll',
	'API-MS-Win-Core-DelayLoad-L1-1-0.dll',
	'API-MS-Win-Core-ErrorHandling-L1-1-0.dll',
	'API-MS-Win-Core-File-L1-1-0.dll',
	'API-MS-Win-Core-Handle-L1-1-0.dll',
	'API-MS-Win-Core-Heap-L1-1-0.dll',
	'API-MS-Win-Core-Interlocked-L1-1-0.dll',
	'API-MS-Win-Core-IO-L1-1-0.dll',
	'API-MS-Win-Core-LibraryLoader-L1-1-0.dll',
	'API-MS-Win-Core-Localization-L1-1-0.dll',
	'API-MS-Win-Core-LocalRegistry-L1-1-0.dll',
	'API-MS-Win-Core-Memory-L1-1-0.dll',
	'API-MS-Win-Core-Misc-L1-1-0.dll',
	'API-MS-Win-Core-ProcessEnvironment-L1-1-0.dll',
	'API-MS-Win-Core-ProcessThreads-L1-1-0.dll',
	'API-MS-Win-Core-Profile-L1-1-0.dll',
	'API-MS-Win-Core-String-L1-1-0.dll',
	'API-MS-Win-Core-Synch-L1-1-0.dll',
	'API-MS-Win-Core-SysInfo-L1-1-0.dll',
	'MSWSOCK.DLL',
	'w9xpopen.exe',
	'wtsapi32.dll',
	'DNSAPI.DLL',
	'USP10.DLL',
	'MSIMG32.DLL'
]

dataFiles = []
dataFiles += generate_data_files('.', '',
	lambda root, name: name == 'settingsspec.ini')
dataFiles += generate_data_files('.', '',
	lambda root, name: re.search('[.]glade$', name) is not None)
dataFiles += generate_data_files('.', 'themes')
dataFiles += generate_data_files(GTK_RUNTIME_DIR, GTK_THEME_ENGINES_DIR, 
	lambda root, name: name == 'libpixmap.dll' or name == 'libclearlooks.dll')
dataFiles += generate_data_files(GTK_RUNTIME_DIR, 'etc')
dataFiles += generate_data_files(GTK_RUNTIME_BIN_DIR, '',
	lambda root, name: name.lower() in gtkDlls)
dataFiles += generate_data_files('.', 'Microsoft.VC90.CRT')
dataFiles += [('.', [os.path.join(GTK_RUNTIME_BIN_DIR, 'zlib1.dll')])]

setup(
	options = {
		'py2exe': {
			'includes': 'cairo, pango, pangocairo, atk, gobject, gio',
			'compressed': 1,
			'optimize': 2,
			'bundle_files': 1,
			'dll_excludes': dllExcludes + gtkDlls
		}
	},
	windows = [windows_target],
	data_files = dataFiles,
	zipfile = None
)
