<?php

echo '
<!DOCTYPE html>
<html>
  <head>
  </head>
  <body>
    <header>
      <h1>Lookup addresses using name or zip. If you put both, it will look for entries that have one or the other</h1>
    </header>
    <form action="customer-form-results">
      Name: 
      <input type="text" name="customer-name"><br>
      Zip:
      <input type="text" name="zip"><br>
      <input type="submit" value="submit">
    </form>
  </body>
</html>';
