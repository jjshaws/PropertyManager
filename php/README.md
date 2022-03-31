<h1> READ ME </h1>

<h2> Set-up environment to use PHP (adapted from Tutorial 7)</h2>

1. SSH into the undergrad servers. For example: 
```
ssh <CWL username>@remote.students.cs.ubc.ca
```

2. Transfer the php folder into your `public_html` folder.

3. Set the following file permisions via the terminal from your root folder:
```
chmod 755 ~/public_html/PropertyManager/php
chmod 755 ~/public_html/PropertyManager/php/milestone-4.php
chmod 755 ~/public_html/PropertyManager/php/style.php
chmod 755 ~/public_html/PropertyManager/php/databaseSetup.sql
```

4. Go to `https://www.students.cs.ubc.ca/~<your CWL username>/PropertyManager/php/milestone-4.php`. This page should load the application.

---

<h2> Populate database (adapted from Tutorial 5)</h2>

5. `cd` into `public_html/PropertyManager/php/` via terminal. Type `stty erase ^h` to allow backspaces in sqlplus.

6. Open up sqlplus in this folder and login
```
sqlplus ora_CWLid@stu

Username: ora_CWLid (note: replace the characters “CWLid” with your actual CWL userid and don’t forget the @stu after your CWL userid)

Password: a12345678 (note: replace the digits “12345678” with your student number)
```

7. Populate the database via `databaseSetup.sql`
```
start databaseSetup.sql
```
