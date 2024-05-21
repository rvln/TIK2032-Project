document.getElementById("addArticleBtn").addEventListener("click", () => {
  const articleForm = document.getElementById("articleForm");
  articleForm.style.display =
    articleForm.style.display === "none" ? "block" : "none";
});
