# Fruit Makhzan

# Description

A database-themed shop-management game.

Players interact with an artistically presented database of fruit items, and make decisions about selling prices and when to restock. Fruit sales are controlled by in-game factors like 'weather' and 'politics'. Day-by-day spending and sales performance are displayed graphically. The player unlocks new fruits at different levels of achievement, and is given the opportunity to create their own fruits to add to the database.

## Instructions

This application is live on [Heroku](http://fruit-makhzan.herokuapp.com/), but you can also download this repository and run the project locally by following these steps:

1. Fork this repository by clicking the button labelled 'Fork' on the [project page](https://github.com/chicorycolumn/Fruit-Makhzan).
   <br/>
   Copy the url of your forked copy of the repository, and run `git clone the_url_of_your_forked_copy` in a Terminal window on your computer, replacing the long underscored word with your url.
   <br/>
   If you are unsure, instructions on forking can be found [here](https://guides.github.com/activities/forking/) or [here](https://www.toolsqa.com/git/git-fork/), and cloning [here](https://www.wikihow.com/Clone-a-Repository-on-Github) or [here](https://www.howtogeek.com/451360/how-to-clone-a-github-repository/).

2. Open the project in a code editor, and run `npm install` to install necessary packages. You may also need to install [Node.js](https://nodejs.org/en/) by running `npm install node.js`.

3. Install [XAMPP](https://www.apachefriends.org/index.html) to get an Apache server. Instructions can be found [here](https://vitux.com/how-to-install-xampp-on-your-ubuntu-18-04-lts-system/). Once all the steps have been followed, on Ubuntu the command `sudo /opt/lampp/lampp start` will start the server.

4. You will need to import the database at [phpmyadmin](http://localhost/phpmyadmin/), and also install [Composer](https://getcomposer.org/download/).

5. Open _localhost/name-of-folder-containing-project-here_ to open the project in development mode.

## Deploy

General instructions for hosting on **Heroku** for **automatic deployment** are as follows:

0. Ensure the project is initialised in a Git repository. If you are unsure what this means, instructions can be found [here](https://medium.com/@JinnaBalu/initialize-local-git-repository-push-to-the-remote-repository-787f83ff999) and [here](https://www.theserverside.com/video/How-to-create-a-local-repository-with-the-git-init-command).

1. Install the Heroku CLI if not already, with `npm install heroku`.

2. Run these three commands:

- `heroku login`
- `heroku create my-awesome-app`
- `heroku git:remote -a my-awesome-app`

3. Login to Heroku and enable automatic deploys from Github, and connect the repo.

Now when you commit and push to Github, Heroku will deploy the latest version of the project automatically.

## Built with

- [PHP](https://www.php.net/) - The primary coding language
- [VisualStudioCode](https://code.visualstudio.com/) - The code editor

- [Heroku](https://www.heroku.com/) - The cloud application platform
- [ClearDB](https://www.cleardb.com/) - The cloud database service

- [MySQL](https://www.mysql.com/) - The database management system
- [Apache](http://httpd.apache.org/) - The web server used in development

- [jQuery](https://jquery.com/) - The JavaScript library used for design and display
