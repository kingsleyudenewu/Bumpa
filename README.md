# Bumpa Pay
A payment solution for GetBumpa to enable users store funds in their wallet, receive payment and transfer funds to other users.


<p>
  <blockquote style="color:red">
    **Please follow the steps below to setup the application on your system** 
  </blockquote>
</p>  

## Required Versions
-PHP 8.1

## Installation Steps

- Clone project
- Run ```composer install``` for the main project
- Rename .env.example to .env
- Create you database and set dbname, username and password on the new .env file
- Input your Flutterwave and Paystack keys on the .env file
- If you are running this locally make sure you install ngrok (```brew install ngrok/ngrok/ngrok```) ```ngrok http http://localhost:8000``` to expose your local server to the internet
- Generate your laravel key : ```php artisan key:generate```
- Run ```php artisan migrate```
- Run ```php artisan db:seed``` to generate dummy data for user
- Here is the url to the postman collection for the API endpoints: https://documenter.getpostman.com/view/910439/2sA3QniZvT
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
