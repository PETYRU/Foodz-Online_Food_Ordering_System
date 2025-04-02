<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Donate to End Hunger</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      color: #333;
    }
    header {
      background: #f8b400;
      padding: 20px;
      text-align: center;
      color: #fff;
    }
    header h1 {
      margin: 0;
    }
    .hero {
      background: url('./images/donate.jpg') center/cover no-repeat;
      color: #fff;
      padding: 100px 20px;
      text-align: center;
    }
    .hero h2 {
      font-size: 2.5em;
      margin-bottom: 10px;
    }
    .hero p {
      font-size: 1.2em;
    }
    .donation-section {
      text-align: center;
      padding: 40px 20px;
      background: #f4f4f4;
    }
    .donation-section input[type="number"] {
      padding: 10px;
      font-size: 1.2em;
      width: 100px;
    }
    .donation-section button {
      padding: 10px 20px;
      background: #f8b400;
      border: none;
      color: #fff;
      font-size: 1.2em;
      cursor: pointer;
      margin-top: 10px;
    }
    .quotes-section {
      padding: 40px 20px;
      text-align: center;
    }
    .quotes-section .quote {
      font-size: 1.1em;
      margin: 20px 0;
      padding: 20px;
      background: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 5px;
      display: inline-block;
      max-width: 600px;
    }
    footer {
      background: #333;
      color: #fff;
      padding: 20px;
      text-align: center;
    }
  </style>
</head>
<body>

<header>
  <h1>Help Us Feed the Hungry</h1>
</header>

<section class="hero">
  <h2>"Hunger is not an issue of charity. It is an issue of justice."</h2>
  <p>Your small donation can make a big difference in someone's life.</p>
  <button onclick="document.getElementById('donation-section').scrollIntoView({behavior: 'smooth'})">Donate Now</button>
</section>

<section id="donation-section" class="donation-section">
  <h2>Make a Donation</h2>
  <p>Enter the amount you wish to donate:</p>
  <input type="number" id="donationAmount" placeholder="10" min="1">
  <button onclick="donate()">Donate Now</button>
</section>

<section class="quotes-section">
  <h2>Inspiring Quotes</h2>
  <div class="quote">"We cannot help everyone, but everyone can help someone." – Ronald Reagan</div>
  <div class="quote">"A single act of kindness throws out roots in all directions." – Amelia Earhart</div>
  <div class="quote">"Your donation is more than money; it's hope for someone in need."</div>
</section>

<footer>
  <p>Thank you for supporting our cause to end hunger.</p>
  <p>Contact us | Privacy Policy | Terms of Service</p>
</footer>

<script>
  function donate() {
    const amount = document.getElementById("donationAmount").value;
    if (amount && amount > 0) {
      alert(`Thank you for donating $${amount}!`);
      // Replace the alert with actual donation processing code
    } else {
      alert("Please enter a valid donation amount.");
    }
  }
</script>

</body>
</html>
