// This is included with the Parsley library itself,
// thus there is no use in adding it to your project.


Parsley.addMessages('vi', {
    defaultMessage: "Giá trị không hợp lệ.",
    type: {
      email:        "Email không hợp lệ.",
      url:          "Url không hợp lệ.",
      number:       "Cần điền đúng định dạng số.",
      integer:      "Nó cần là số nguyên.",
      digits:       "Nó cần là chữ số.",
      alphanum:     "Nó cần là chữ cái."
    },
    notblank:       "Không được để trống.",
    required:       "Giá trị là bắt buộc.",
    pattern:        "Giá trị không hợp lệ.",
    min:            "Nên lớn hơn hoặc bằng %s.",
    max:            "Nên bé hơn hoặc bằng %s.",
    range:          "Giá trị cần ở giữa %s và %s.",
    minlength:      "Quá ngắn. Nó nên có %s kí tự hoặc nhiều hơn.",
    maxlength:      "Quá dài. Nó nên có %s kí tự hoặc ít hơn.",
    length:         "Độ dài không được phép. Nó nên ở giữa %s và %s kí tự.",
    mincheck:       "Bạn phải chọn ít nhất %s lựa chọn.",
    maxcheck:       "Bạn phải chọn %s lựa chọn hoặc nhiều hơn.",
    check:          "Bạn phải chọn giữa %s và %s lựa chọn.",
    equalto:        "Giá trị cần giống nhau."
  });
  
  Parsley.setLocale('vi');
  