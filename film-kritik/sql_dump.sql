
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS reviews;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE movies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  year INT,
  genre VARCHAR(100),
  director VARCHAR(255),
  actors TEXT,
  description TEXT,
  poster VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  movie_id INT,
  rating INT CHECK (rating BETWEEN 1 AND 10),
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO movies (title, year, genre, director, actors, description, poster) VALUES
('Inception', 2010, 'Sci-Fi', 'Christopher Nolan',
 'Leonardo DiCaprio, Joseph Gordon-Levitt, Ellen Page',
 'A thief who steals corporate secrets through dream-sharing technology.',
 'inception.jpg'),
('Interstellar', 2014, 'Sci-Fi', 'Christopher Nolan',
 'Matthew McConaughey, Anne Hathaway, Jessica Chastain',
 'A team travels through a wormhole to ensure humanity''s survival.',
 'interstellar.jpg'),
('The Dark Knight', 2008, 'Action', 'Christopher Nolan',
 'Christian Bale, Heath Ledger, Aaron Eckhart',
 'Batman faces the Joker, a criminal mastermind.',
 'dark_knight.jpg'),
('Fight Club', 1999, 'Drama', 'David Fincher',
 'Brad Pitt, Edward Norton, Helena Bonham Carter',
 'An underground fight club evolves into something sinister.',
 'fight_club.jpg'),
('The Matrix', 1999, 'Sci-Fi', 'Wachowski Sisters',
 'Keanu Reeves, Laurence Fishburne, Carrie-Anne Moss',
 'A hacker discovers the nature of reality.',
 'matrix.jpg');
