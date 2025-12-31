# Shortcode UI & Form Builder Plugin

This plugin adds a powerful set of UI components and a **Dynamic Form Builder** to your Markdown content.

## 🚀 Form Builder (CF7 Style)
Create working contact forms directly in Markdown. Submissions are stored securely in `storage/messages`.

```markdown
[form]
  [input type="text" name="full_name" label="Name" required="true"]
  [input type="email" name="email" label="Email"]
  [select name="topic" label="Topic" options="Sales,Support"]
  [textarea name="body" label="Message"]
  [checkbox name="agree" label="I agree to terms"]
  [submit label="Send Now"]
[/form]
```

### Components
*   `[form]...[/form]`: Wraps the form. Handles CSRF and Anti-Spam automatically.
*   `[input type="..." name="..." label="..."]`: Standard inputs (text, email, tel, date).
*   `[textarea name="..." label="..."]`: Multi-line text.
*   `[select name="..." options="Option1,Option2"]`: Dropdown menu.
*   `[checkbox name="..." label="..."]`: Single checkbox.
*   `[submit label="..."]`: Submit button.

---

## 🎨 UI Shortcodes

### 1. Structure
*   **Grid:** `[grid][col]Left[/col][col]Right[/col][/grid]` - Creates a 2-column layout.
*   **Card:** `[card title="My Card"]Content[/card]` - A box with shadow.
*   **Divider:** `[divider]` - A horizontal line.

### 2. UI Elements
*   **Alerts:** `[alert type="info|success|warning|danger"]Message[/alert]`
*   **Buttons:** `[button url="/contact" color="primary"]Click Me[/button]`
*   **Badges:** `[badge color="red"]Hot[/badge]`
*   **Progress:** `[progress value="70" color="blue"]`
*   **Icons:** `[icon name="check|star|user|heart|settings"]`

### 3. Content
*   **YouTube:** `[youtube id="dQw4w9WgXcQ"]`
*   **Hero:** `[hero]Big Title[/hero]`
*   **Details:** `[details summary="Click to expand"]Hidden content[/details]`

### 4. Data & Stats
*   **Stat:** `[stat value="10k" label="Users"]`
*   **Price:** `[price plan="Pro" cost="$29" period="/mo" button="Buy"]Features[/price]`
*   **User:** `[user name="Alice" role="CEO" img="..."]Quote[/user]`