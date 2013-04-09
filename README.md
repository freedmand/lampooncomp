Harvard Lampoon Comp Submission Tool
====================================

This Lampoon Comp submission tool is designed to showcase my overall programming talent across multiple languages and paradigms with an emphasis on web. This submission tool is a basic website that allows compers to submit image and text pieces online, and comp directors to view and evaluate these pieces.

Given the tight time constraints (though I worked tirelessly) this is not an entirely complete website. There are still various glitches and some files had to be prepared hastily to make this deadline. That said, this project showcases the following features:
* A heavy emphasis on security. There is a login and registration system, an email verification system, and various hash-based security measures. Only those with appropriate permissions can view certain pages. I use cookies to store persistent login information for up to 2 weeks.
* An emphasis on simplistic style. There were various design considerations that went into the making of this website such as striving for conciseness and making the existing Lampoon color scheme slightly more pleasant as a web design template. I did not use Bootstrap in the making of this website as it was an effort to relieve myself of the overly clean and overused look Bootstrap gives. That said, in future work on this website, I would likely go in and redesign everything with a Bootstrap template to harness their typographic correctness (spacings) and beautiful display formats on smallers screens.
* A rich text editor designed from scratch. This was not primarily an effort to impress in the comp process, but more of a practical matter in tackling a problem few have dealt with: to emphasize content in the lit comp as opposed to format I decided to restrict my text editor to three buttons - bold, italic, and underline. The font was fixed to 'Times New Roman.'
* A commenting system designed from scratch. Built in the spirit of Google docs commenting system, this mechanism allows compers and directors to view over pieces and submit textual comments or image-based comments. Text comments consist of a region of highlighted text, and image comments a rectangular shaded region in which users can note peculiarities or comments about one's piece.
* Unique interface design. There are some very unique design choices made in this website. For instance, the image uploader show's the image instantly loaded locally from the user's computer while a purple semi-transparent screen slowly reveal the contents.
* MySQL harmony. All the MySQL databases in this project were very carefully designed to interact with one another. Foreign keys are used frequently and databases cascade such that an update in one database will update all others.
* Creating vector logos and graphics from scratch. Throughout the creation of this website, I redesigned the Harvard Lampoon logo in vector graphics, created all the icons in use on the website, and created a vector silhouette graphic of the top of the Lampoon castle and Ibis. This was done in programs "Art Text 2" and "Inkscape."
* Sheer magnitude. Though the project is incomplete, there are thousands of lines of code written in the span of a couple of weeks.

MySQL database structures
-------------------------
*users* table. Stores information about a particular user including his name, email, hashed password, room, year, board, and admin status. The table was created with the following command:

CREATE TABLE users(id INT, id_incr INT NOT NULL AUTO_INCREMENT, name TEXT, email VARCHAR(154), passwordhash TEXT, room TEXT, year SMALLINT UNSIGNED, board TEXT, registered TINYINT(1) NOT NULL DEFAULT 0, director TINYINT(1) NOT NULL DEFAULT 0, PRIMARY KEY (id_incr), UNIQUE (id), UNIQUE(email)) ENGINE=InnoDB;

*cookiestore* table. Stores persistent login cookies for a particular user as well as that cookie's expiration time. Created with the following command:

CREATE TABLE cookiestore(id INT, rand VARCHAR(64) NOT NULL, expiry DATETIME, FOREIGN KEY (id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE) ENGINE=InnoDB;

*articles* table. Stores articles written by a particular user as well as its title and the path in which it is stored. Created with the following command:

CREATE TABLE articles(id INT, article_id INT, article_id_incr INT NOT NULL AUTO_INCREMENT, title TEXT, istext TINYINT(1) NOT NULL, path TEXT, PRIMARY KEY (article_id_incr), UNIQUE(article_id), FOREIGN KEY (id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE) ENGINE=InnoDB;

*feedback* table. Stores feedback and comments about a particular user's article. Created with the following command:

CREATE TABLE feedback(id INT, article_id INT, feedback_id_incr INT NOT NULL AUTO_INCREMENT, author_id INT, data TEXT, start INT, end INT, start_y INT, end_y INT, PRIMARY KEY (feedback_id_incr), FOREIGN KEY (id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE, FOREIGN KEY (article_id) REFERENCES articles(article_id) ON UPDATE CASCADE ON DELETE CASCADE, FOREIGN KEY (author_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE) ENGINE=InnoDB;

Future consideration
--------------------
There is still much work to be done. Going forward, I would like to interact with more preexisting systems such as Drupal and Bootstrap. The primary motivation for reinventing the wheel as I have done so thoroughly in some instances in this project was to have the most control over the security and design features and remain uncorrupted by laziness sourced from external modules. Overall, this entire project has been a fantastic learning experience.

The Github repo can be found here: https://github.com/freedmand/lampooncomp
And the project is live online at: freedmand.com/lampooncomp/

This project was tested mainly in Google Chrome on a MacBook Pro.
