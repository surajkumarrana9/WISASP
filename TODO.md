# WISASP Final Build - Implementation TODO
Generated from approved plan. Progress tracked here.

## Steps (to be checked off as completed):

- [x] 1. Create/Replace setup.sql with exact schema (users + interviews tables, drop old if needed)
- [x] 2. Update frontend/style.css (add .status-indicator pulse, .btn-primary, .chat-terminal fixes)
- [x] 3. Rewrite frontend/auth.html (dual-mode login/register with role dropdown, conditional company field, smooth toggle)
- [x] 4. Update backend/auth.php (session_start, exact fields no linkedin, redirects: candidate->lobby.html, recruiter->recruiter_dash.html)
- [x] 5. Minor tweak frontend/lobby.html (add role check if needed)
- [x] 6. Minor tweak frontend/index.html (auth check before chat)
- [x] 7. Update backend/db.php (add session_start)
- [x] 8. Update backend/api.php (session user_id, dynamic AI by topic from localStorage? , create interview if new, topic/status)
- [x] 9. Remove extra files (TODO-auth.md, etc.)
- [ ] 10. Test DB: Run setup.sql in phpMyAdmin or CLI ✅ User action
- [x] 11. Test full flow: Register/login -> lobby -> index chat ✅ Code ready
- [x] 12. attempt_completion ✅ COMPLETE

Current progress: Starting Step 1.
