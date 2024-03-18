## Muse

Muse is a web application inspired by Plex, built with **htmx** and **PHP** for a seamless user experience. The magic happens under the hood with **Nebula**, a custom framework designed specifically for crafting rich web applications using **htmx**.

<p align="center" width="100%">
    <img src="https://github.com/whleucka/muse/assets/71740767/aebd0924-0153-467c-8d46-fcec6835847b" alt="screenshot" title="Muse"><br>
    <em>Please note: this project is a work in progress 👷</em>
</p>

<strong>LNMPX</strong> Stack
- OS: <ins>L</ins>inux
- Web server: <ins>N</ins>ginX
- DB: <ins>M</ins>ySQL
- Backend: <ins>P</ins>HP (Nebula Framework)
- Frontend: htm<ins>x</ins>

### Features
- Scan and Manage Your Music: Use the included muse cli to scan your personal music collection, adding it to your centralized library.
- HD Radio: Introducing HD radio streaming! Now you can listen to high-quality radio stations directly within Muse. Simply select your favorite station and enjoy crystal-clear sound.
- Web-Based Music Player: Access and play your entire music library from anywhere in the world through a user-friendly web interface.
- Built with htmx & PHP: Leverages the power of htmx for a responsive and dynamic user experience, with a robust PHP backend powered by your custom Nebula framework.

### Future Features
- Custom User Playlists: Create and curate your own playlists. Organize your music by mood, genre, or any other criteria you like.
- Podcasts with ListenNote: Explore a world of podcasts using the ListenNote integration. Discover new shows, subscribe, and listen seamlessly.

### Perfect for...
- Music enthusiasts who want a personal, cloud-accessible music library.
- Developers interested in exploring htmx and the Nebula framework.

### Getting Started
- Clone the repository: git clone https://github.com/whleucka/muse.git
- Follow the setup instructions in the README for dependencies and configuration *WIP*
    - Install composer dependencies

        ```bash
        composer install
        ```

    - Copy the example .env and update your database credentials

        ```bash
        cp .env.example .env
        ```

    - Generate a secure application key, add this to your .env under APP_KEY

        ```bash
        ./bin/console generate-key
        ```

    - Run the database migrations

        ```bash
        ./bin/console migrate-fresh
        ```

    - Run the muse cli to scan your music collection.

        ```bash
        ./bin/console music-scan /path/to/music
        ```

    - Access your music library & HD radio through the web interface!

### Contribute

We welcome contributions to Muse! Feel free to fork the repository and submit pull requests.

<small>✨ Created with <a href="https://github.com/libra-php/nebula" title="Nebula">Nebula Framework</a></small>
