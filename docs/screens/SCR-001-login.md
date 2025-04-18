# SCR‑001: Login Screen

**Route:** `/login`  
**Type:** Public

### Layout
- Email input  
- Password input  
- “Forgot password?” link  
- Submit button  

### User Flow
1. User enters credentials and clicks “Log in.”  
2. On failure: show inline validation messages.  
3. On success: redirect to `/dashboard`.  

### Requirements
- Must call Laravel’s auth endpoint (uses Breeze controllers) :contentReference[oaicite:0]{index=0}&#8203;:contentReference[oaicite:1]{index=1}  
- Enforce HTTPS and rate‑limit login attempts (security NFR)  

### Wireframe
![Login Wireframe](../assets/wireframes/login.png)
