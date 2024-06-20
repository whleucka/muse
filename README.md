## Muse

Muse is a web application inspired by *Plex*, crafted with **htmx** and **PHP** to provide a seamless user experience.

<div align="center">
    <img alt="screen" src="https://github.com/whleucka/muse/assets/71740767/0bc08dfa-512f-4dbb-a7e3-d4a42126241a" width="100%" style="margin-right: 5px;" /><br>
    <em>Note: work in progress 👷</em>
</div>

### Features
- Scan and Manage Your Music: Use the included muse cli to scan your personal music collection, adding it to your centralized library.
- HD Radio: Introducing HD radio streaming! Now you can listen to high-quality radio stations directly within Muse. Simply select your favorite station and enjoy crystal-clear sound.
- Web-Based Music Player: Access and play your entire music library from anywhere through a user-friendly web interface.
- Podcasts with ListenNote: Explore a world of podcasts using the ListenNote integration.
- Built with htmx & PHP: Leverages the power of htmx for a responsive and dynamic user experience, with a robust PHP backend powered by your custom Nebula framework.
- Light/Dark Theme: Personalize your experience with light and dark theme options. Switch between themes effortlessly to match your environment and reduce eye strain during extended listening sessions.

### To do
- Custom User Playlists: Create and curate your own playlists. Organize your music by mood, genre, or any other criteria you like.

### Perfect for...
- Music enthusiasts who want a personal, cloud-accessible music library.
- Developers interested in exploring htmx and the Nebula framework.

### Getting Started
- Clone the repository: `git clone https://github.com/whleucka/muse.git`
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

    - Run the muse cli to scan your music collection. This could take a while, depending on how many files require id3 tag analysis.

        ```bash
        ./bin/console music-scan /path/to/music
        ```

    - Create a storage directory and give the proper permissions. Create a sym link in the public directory

        ```bash
        mkdir ./storage
        chown -R www-data:www-data storage
        cd public && ln -s ../storage
        ```

    - Access your music library, podcasts, or HD radio through the web interface!

### Contribute

We welcome contributions to Muse! Feel free to fork the repository and submit pull requests.

<small>✨ Created with <a href="https://github.com/libra-php/nebula" title="Nebula">Nebula</a></small>
