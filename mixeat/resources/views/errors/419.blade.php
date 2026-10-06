<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Expired | MixEat</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; color: #171717; background: #fff9e6; }
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 24px; box-sizing: border-box; }
        main { width: min(100%, 520px); padding: 40px; border: 1px solid #e8dfaf; border-radius: 24px; background: #fff; box-shadow: 0 20px 60px rgb(229 169 0 / 12%); text-align: center; box-sizing: border-box; }
        .brand { display: inline-block; margin-bottom: 24px; padding: 8px 14px; border-radius: 10px; background: #ffc60a; color: #090909; font-weight: 800; letter-spacing: .04em; }
        .code { margin: 0; color: #a87800; font-size: 14px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        h1 { margin: 10px 0; font-size: clamp(26px, 6vw, 34px); }
        p { color: #555; line-height: 1.65; }
        .actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; margin-top: 28px; }
        a { display: inline-block; padding: 12px 18px; border: 1px solid #e5a900; border-radius: 999px; color: #171717; font-weight: 700; text-decoration: none; }
        a.primary { border-color: #ffc60a; background: #ffc60a; }
        a:hover { background: #fff4bf; }
    </style>
</head>
<body>
    <main>
        <span class="brand">MIX EAT</span>
        <p class="code">Error 419</p>
        <h1>Your session has expired</h1>
        <p>The form was not submitted because this page had been open too long. Reopen the page and sign in again if prompted. Any unsaved changes may need to be entered again.</p>
        <div class="actions">
            <a class="primary" href="{{ route('admin.catering.marketing') }}">Reopen Catering Admin</a>
            <a href="{{ route('login') }}">Sign in</a>
            <a href="{{ route('home') }}">Back to MixEat</a>
        </div>
    </main>
</body>
</html>
