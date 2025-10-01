import re
from playwright.sync_api import sync_playwright, expect

def run_verification(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    BASE_URL = "http://127.0.0.1:8000"

    # --- 1. Home Page ---
    page.goto(f"{BASE_URL}/index.php")
    page.screenshot(path="jules-scratch/verification/01_home_page.png")

    # --- 2. Registration ---
    # Register a new User
    page.goto(f"{BASE_URL}/register.php")
    page.get_by_label("Name").fill("Test User")
    page.get_by_label("Email").fill("user@example.com")
    page.get_by_label("Password").fill("password123")
    page.get_by_label("Role").select_option("User")
    page.get_by_role("button", name="Register").click()
    expect(page.get_by_text("Registration successful!")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/02_register_user_success.png")

    # Register a new Admin
    page.goto(f"{BASE_URL}/register.php")
    page.get_by_label("Name").fill("Admin User")
    page.get_by_label("Email").fill("admin@example.com")
    page.get_by_label("Password").fill("adminpass")
    page.get_by_label("Role").select_option("Admin")
    page.get_by_role("button", name="Register").click()
    expect(page.get_by_text("Registration successful!")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/03_register_admin_success.png")

    # --- 3. User Login and Dashboard ---
    page.goto(f"{BASE_URL}/login.php")
    page.get_by_label("Email").fill("user@example.com")
    page.get_by_label("Password").fill("password123")
    page.get_by_role("button", name="Login").click()
    expect(page.get_by_text("Welcome, Test User!")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/04_user_dashboard.png")

    # --- 4. User Profile Page ---
    page.get_by_role("link", name="Update Profile").click()
    expect(page.get_by_text("Your Profile")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/05_user_profile_page.png")

    # --- 5. User Request Blood Page ---
    page.get_by_role("link", name="Request Blood").click()
    expect(page.get_by_text("Submit a New Request")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/06_user_request_blood_page.png")

    page.get_by_role("link", name="Logout").click()

    # --- 6. Admin Login and Dashboard ---
    page.goto(f"{BASE_URL}/login.php")
    page.get_by_label("Email").fill("admin@example.com")
    page.get_by_label("Password").fill("adminpass")
    page.get_by_role("button", name="Login").click()
    expect(page.get_by_text("Admin Dashboard")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/07_admin_dashboard.png")

    # --- 7. Admin Manage Users ---
    page.get_by_role("link", name="Manage Users").click()
    expect(page.get_by_text("Manage Users")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/08_admin_manage_users.png")

    # --- 8. Admin Manage Requests ---
    page.get_by_role("link", name="Manage Requests").click()
    expect(page.get_by_text("Manage Blood Requests")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/09_admin_manage_requests.png")

    # --- 9. Admin Manage Inventory ---
    page.get_by_role("link", name="Manage Inventory").click()
    expect(page.get_by_text("Manage Blood Inventory")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/10_admin_manage_inventory.png")

    context.close()
    browser.close()

with sync_playwright() as playwright:
    run_verification(playwright)