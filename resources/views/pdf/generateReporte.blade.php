<div>
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    <h1>Reporte producto</h1>

    <h2>Nombre: {{ $productoFormated['nombre'] }}</h2>
    <p>Precio: {{ $productoFormated['precio'] }}</p>
    <p>Estado: {{ $productoFormated['estado'] ? 'Activo' : 'Inactivo' }}</p>
    <h3>Categoría</h3>
    <p>Nombre: {{ $productoFormated['categoria']['nombre'] ?? 'N/A' }}</p>
    <h3>Inventario</h3>
    <p>Cantidad: {{ $productoFormated['inventario']['cantidad'] ?? 0 }}</p>
    <h3>Imágen</h3>
    <img src="{{ $productoFormated['image']['path'] }}" alt="{{ $productoFormated['image']['nombre'] }}">
</div>
