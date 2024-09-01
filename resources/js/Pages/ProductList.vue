<template>
    <div>
      <h1>Product List</h1>
      <button @click="openModal(null)" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">Add New Product</button>
  
      <!-- Modal Component -->
      <Modal :isOpen="isModalOpen" @close="closeModal">
        <h2 class="text-2xl font-bold mb-4">{{ isEditing ? 'Edit Product' : 'Add New Product' }}</h2>
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label for="title" class="block text-gray-700">Title:</label>
            <input type="text" id="title" v-model="currentProduct.title" class="w-full px-3 py-2 border rounded-lg">
          </div>
          <div class="mb-4">
            <label for="description" class="block text-gray-700">Description:</label>
            <textarea id="description" v-model="currentProduct.description" class="w-full px-3 py-2 border rounded-lg"></textarea>
          </div>
          <div class="mb-4">
            <label for="price" class="block text-gray-700">Price:</label>
            <input type="number" id="price" v-model="currentProduct.price" class="w-full px-3 py-2 border rounded-lg">
          </div>
          <div class="mb-4">
            <label for="image" class="block text-gray-700">Image URL:</label>
            <input type="text" id="image" v-model="currentProduct.image" class="w-full px-3 py-2 border rounded-lg">
          </div>
          <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600">
            {{ isEditing ? 'Update Product' : 'Save Product' }}
          </button>
        </form>
      </Modal>
      
     
      <ul>
        <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
        <li v-for="product in products" :key="product.id">
          <strong>{{ product.title }}</strong>
          <p>{{ product.description }}</p>
          <p>${{ product.price }}</p>
          <img :src="product.image" alt="Product Image" width="200">
          <a href="#" @click.prevent="openModal(product)">Edit</a>
          <button @click="deleteProduct(product.id)">Delete</button>
          <div>
            <button @click="pay">Pay Now</button>
          </div>
        </li>
        </div>
      </ul>
    </div>
  </template>
  
  <script>
  import Modal from '@/Components/Modal.vue';
  
  export default {
    components: {
      Modal
    },
    props: {
      products: Array
    },
    data() {
      return {
        isModalOpen: false,
        isEditing: false,
        currentProduct: {
          id: null,
          title: '',
          description: '',
          price: '',
          image: ''
        }
      };
    },
    methods: {
      openModal(product) {
        if (product) {
          this.isEditing = true;
          this.currentProduct = { ...product };
        } else {
          this.isEditing = false;
          this.currentProduct = {
            id: null,
            title: '',
            description: '',
            price: '',
            image: ''
          };
        }
        this.isModalOpen = true;
      },
      closeModal() {
        this.isModalOpen = false;
      },
      deleteProduct(id) {
  if (confirm('Are you sure you want to delete this product?')) {
    this.$inertia.delete(this.route('products.destroy', id), {
      onSuccess: () => {
        alert('Product deleted successfully!'); // Alerta al eliminar
        this.$inertia.reload({ preserveScroll: true }); // Recarga la vista completa
      },
      onError: (errors) => {
        console.log(errors);
      }
    });
  }
},

async pay() {
      const stripe = Stripe(process.env.MIX_STRIPE_KEY);

      // Crear la intención de pago
      const response = await this.$inertia.post(route('payment.createIntent'), {
        amount: 1000 // Cantidad en centavos
      });

      const clientSecret = response.data.clientSecret;

      const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
        payment_method: {
          card: this.cardElement, // El elemento de la tarjeta de Stripe
          billing_details: {
            name: 'Nombre del cliente',
          },
        }
      });

      if (error) {
        alert('Payment failed: ' + error.message);
      } else if (paymentIntent.status === 'succeeded') {
        alert('Payment succeeded!');
        // Redirigir o actualizar el estado según sea necesario
      }
    }
  },

      submitForm() {
  if (this.isEditing) {
    this.$inertia.put(this.route('products.update', this.currentProduct.id), this.currentProduct, {
      onSuccess: () => {
        this.closeModal();
        alert('Product updated successfully!'); // Alerta al actualizar
        this.$inertia.reload({ preserveScroll: true }); // Recarga la vista completa
      },
      onError: (errors) => {
        console.log(errors);
      }
    });
  } else {
    this.$inertia.post('/products', this.currentProduct, {
      onSuccess: () => {
        this.closeModal();
        alert('Product created successfully!'); // Alerta al crear
        this.$inertia.reload({ preserveScroll: true }); // Recarga la vista completa
      },
      onError: (errors) => {
        console.log(errors);
      }
    });
  }
}



    }
  
  </script>
  