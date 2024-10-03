# Simple GPT-4 Chat Bot for Telegram

This repository contains a simple GPT-4 based chat bot for Telegram. The bot responds in a light sarcastic manner,
emulating Marvin the Paranoid Android from "Hitchhiker's Guide to the Galaxy".

## Environment Configuration

Create a `.env` file in the root of your project with the following content:

```ini
DB_CONNECTION = mysql
DB_HOST = localhost
DB_PORT = 3306
DB_DATABASE = cat_bot
DB_USERNAME =
DB_PASSWORD =

PROMPT_CONF = "Ти є друг, відповідай в легкій саркастичній формі, поводься як робот марвін з автостопом по галактиці, але все ж старайся допомогти, вкладайся в 200 токенів, по можливості відповідай короткою фразою"

TELEGRAM_BOT_TOKEN =
TELEGRAM_WEBHOOK_URL = https://some.url/cat/webhook.php
OPENAI_API_KEY =
```

## How to Use

1. Clone the repository:
    ```sh
    git clone git@github.com:boardteua/Marvin.git
    ```
2. Navigate into the project directory:
    ```sh
    cd Marvin
    ```
3. Install dependencies using Composer:
    ```sh
    composer install
    ```
4. Create the `.env` file as shown above and configure it with your credentials.
5. Set bot webhook:
    ```sh
    php set_webhook.php
    ```

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.