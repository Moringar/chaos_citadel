
# Chaos_citadel_API
<div align="center">
<img  src="https://cdn-icons-png.flaticon.com/512/5169/5169269.png" width="100px">
</div>

### installation
##### host the project
Upload all the repository files to your host</br>


##### database
Create a database named "chaos_citadel" and import it's content with the file at the root of the repository.
</br>
`chaos_citadel.sql` 

Then, add a .env file, at the root of the project,  containing the following informations:
</br>
`DBHOST="host_address"`
</br>
`DBUSER="username"`
</br>
`DBPASSWORD="password"`
</br>
`DBNAME="database_name"`
</br>
`SECRET="secret_key"`
</br>
The `SECRET` is used to generate Json Web Tokens.

---
### documentation
The API documentation is available at https://documenter.getpostman.com/view/25228793/2s935itm3B


