serve:
	php -S localhost:8081 -d .

db:
	wget -nc ftp://wirelessftp.fcc.gov/pub/uls/complete/l_amat.zip -O fcc_uls/fcc_licenses_weekly.zip || true
	unzip fcc_uls/fcc_licenses_weekly.zip EN.dat
	mv EN.dat fcc_uls/EN.dat
	sed -e 's/\r//g' -e 's/"/""/g' -e 's/|/","/g' -e 's/^/"/g' -e 's/$$/"/' < fcc_uls/EN.dat | awk -v OFS='","' -k '{ print "\"" $$5 "\",\"" $$8 "\",\""  $$16 " " $$17 " " $$18 " " $$19 "\""}' > LICENSES_nohead.csv
	rm -rf LICENSES_nohead.csv
	echo "Downloaded database and parsed input into LICENSES.CSV"
	head -n 3 LICENSES.csv

icons:
	unalias mv || true
	wget -nc https://github.com/lucide-icons/lucide/archive/refs/heads/main.zip -O lucide.zip || true
	unzip lucide.zip -d lucide
	mkdir -p assets/lucide_icons
	mv -f lucide/lucide-main/icons/*.svg assets/lucide_icons
	rm -rf lucide.zip lucide
	@echo "Downloaded Lucide icons!"
