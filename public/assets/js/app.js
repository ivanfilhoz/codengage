(function () {
  var orderItemsForm = $('#ce-orderitems-form')[0]

  if (orderItemsForm) {
    var state = []
    var list = $('#ce-orderitems-list')[0]
    var sample = $('#ce-orderitems-sample')[0]
    var input = $('#ce-orderitems-input')[0]
    var alert = $('#ce-orderitems-alert')[0]
    var products = document.querySelectorAll('.ce-orderitems-product')

    products.forEach(function (product) {
      orderItemsForm.addEventListener('submit', function (event) {
        if (!state.length) {
          event.preventDefault()
          $(alert).slideDown()
        }
      })

      product.addEventListener('click', function (event) {
        var id = product.dataset.id

        event.preventDefault()
        state.push({
          id: id,
          quantity: 1,
          percentDiscount: 0
        })
        updateState()

        var item = sample.cloneNode(true)
        var _ = function (role) {
          return item.querySelector('[data-role=' + role + ']')
        }

        _('product').innerText = product.innerText
        _('unitPrice').innerText = product.dataset.formattedPrice
        _('quantity').addEventListener('change', function (event) {
          findById(state, id).item.quantity = event.target.value * 1
          updateState()
        })
        _('percentDiscount').addEventListener('change', function (event) {
          findById(state, id).item.percentDiscount = event.target.value * 1
          updateState()
        })
        _('delete').addEventListener('click', function (event) {
          event.preventDefault()
          state.splice(findById(state, id).index, 1)
          item.remove()
          updateState()
        })

        item.id = ''
        item.style.display = ''
        list.appendChild(item)

        $(alert).slideUp()
      })
    })

    function updateState () {
      input.value = JSON.stringify(state)
    }
  }

  function findById (list, id) {
    for (var index in list) {
      var item = list[index]

      if (item.id === id) {
        return {
          index: index,
          item: item
        }
      }
    }
    return null
  }
})()
