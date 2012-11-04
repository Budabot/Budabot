# -*- mode: python -*-

###############################################################################
# Example usage:
# python E:\Programs\pyinstaller-dev\pyinstaller.py botmanager.spec
# Before running make sure you delete 'build' dir, e.g:
# rmdir /S /Q build && python E:\Programs\pyinstaller-dev\pyinstaller.py botmanager.spec
###############################################################################

# Gotten from: http://stackoverflow.com/questions/11322538/including-a-directory-using-pyinstaller
def extra_datas(mydir):
    def rec_glob(p, files):
        import os
        import glob
        for d in glob.glob(p):
            if os.path.isfile(d):
                files.append(d)
            rec_glob("%s/*" % d, files)
    files = []
    rec_glob("%s/*" % mydir, files)
    extra_datas = []
    for f in files:
        extra_datas.append((f, f, 'DATA'))
    return extra_datas

datas = []
datas += [('addbotwizard.glade', 'addbotwizard.glade', 'DATA')]
datas += [('botwindow.glade',    'botwindow.glade',    'DATA')]
datas += [('configwindow.glade', 'configwindow.glade', 'DATA')]
datas += [('controlpanel.glade', 'controlpanel.glade', 'DATA')]
datas += [('settingsspec.ini',   'settingsspec.ini',   'DATA')]

datas += extra_datas('icon')
datas += extra_datas('themes')

enginesPath = os.path.join('lib', 'gtk-2.0', '2.10.0', 'engines')
absEnginesPath = os.path.join(sys.prefix, 'Lib', 'site-packages', 'gtk-2.0', 'runtime', enginesPath)

binaries = []
binaries += [(os.path.join(enginesPath, 'libclearlooks.dll'), os.path.join(absEnginesPath, 'libclearlooks.dll'), 'BINARY')]
binaries += [(os.path.join(enginesPath, 'libpixmap.dll'),     os.path.join(absEnginesPath, 'libpixmap.dll'),     'BINARY')]

a = Analysis(['__main__.py'],
             hiddenimports=[],
             hookspath=None)

pyz = PYZ(a.pure)
exe = EXE(pyz,
          a.scripts,
          a.binaries + binaries,
          a.zipfiles,
          #a.datas,
          datas,
          name=os.path.join('dist', 'botmanager.exe'),
          debug=False,
          strip=None,
          upx=True,
          console=True,
          icon='icon/icon.ico')
